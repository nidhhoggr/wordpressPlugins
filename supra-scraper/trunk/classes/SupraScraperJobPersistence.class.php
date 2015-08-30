<?php
class SupraScraperJobPersistence {

    private $debug_msgs = array(); 

    function __construct() {
        $this->refreshJobs();
    }

    public function refreshJobs() {
        $this->jobs = (array) get_option('sscrap_jobs');
    }

    public function refreshCurrentJob()
    {
        $this->refreshJobs();

        if(isset($this->currentJobFilename))
        {
            $currentJob = $this->jobs[$this->currentJobFilename];

            $this->currentJob = $currentJob;
        } 
    }

    public function updateJobs() {
        return update_option('sscrap_jobs',$this->jobs);
    }

    public function saveJobByFilename($fileName, $postId) {

        //if the job for the file exists
        if($job = $this->getJobByFilename($fileName)) {

            //if the post id isn't already stored
            if(!in_array($postId,$job)) { 
                //store it
                $this->jobs[$fileName][] = $postId;
            } else {
                return true;
            }
            //job for file doesnt exist
        } else {
            $this->jobs[$fileName] = array($postId);
        }

        return $this->updateJobs();
    }

    public function deleteJobByFilename($fileName) {

        if(!$job = $this->getJobByFilename($fileName)) {
            return false;
        }
        else {
            unset($this->jobs[$fileName]);
            return $this->updateJobs();
        }
    }

    public function removePostIdFromJob($fileName, $postId, $job = false) {

        if(!$job && !$job = $this->getJobByFilename($fileName)) 
        {
            return false;
        }
        else 
        {
            if(($postIdKey = array_search($postId, $job)) !== FALSE) 
            {
                unset($job[$postIdKey]);

                $this->jobs[$fileName] = $job;

                return $this->updateJobs();
            }

            return false;
        }
    }

    public function getJobByFilename($fileName) {
        $job = false;

        //does the filename have a job?
        if(array_key_exists($fileName,$this->jobs)) {
            $job = $this->jobs[$fileName];
        }

        //remove posts from the job if they no longer exists 
        foreach($job as $post_id)
        {
            if(!is_object(get_post($post_id)))
            {
                //provideing the job prevents infinte loop
                $success = $this->removePostIdFromJob($fileName, $post_id, $job);

                if($success)
                {
                    $this->_debug("$fileName was removed from the job because the post id $post_id no longer exists. This may cause issues. You should probbably rename the CSV file of this job");
                }
            }
        }

        $this->currentJob = $job;

        $this->currentJobFilename = $fileName;

        return $job;
    }

    private function _debug($msg) 
    {
        $this->debug_msgs[] = $msg;
    }

    public function getDebugMsgs()
    {
        return $this->debug_msgs;
    }

    public function getCurrentJob() {
        return $this->currentJob;
    }

    public function removePostIdByPostId($postId) {
        foreach($this->jobs as $fileName=>$posts) {
            foreach((array)$posts as $post_id) {
                if($post_id == $postId) {
                    return $this->removePostIdFromJob($fileName, $postId);
                } 
            }
        }
    }

    public function jobOfFilenameHasPosts($fileName) {

        if(!$job = $this->getJobByFilename($fileName)) {
            return false;
        } 
        else {
            return (count($job) > 0);
        }  
    }
}
