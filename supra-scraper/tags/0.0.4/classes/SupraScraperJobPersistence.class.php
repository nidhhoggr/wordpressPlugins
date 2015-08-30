<?php
class SupraScraperJobPersistence {

  function __construct() {
    $this->refreshJobs();
  }

  public function refreshJobs() {
    $this->jobs = (array) get_option('sscrap_jobs');
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

  public function removePostIdFromJob($fileName, $postId) {

    if(!$job = $this->getJobByFilename($fileName)) {
      return false;
    }
    else {
      if($postIdKey = array_search($postId, $job)) {
        unset($job[$postIdKey]);
        $this->jobs[$fileName] = $job;
        return $this->updateJobs();
      }
      return false;
    }
  }

  public function getJobByFilename($fileName) {
    $job = false;

    if(array_key_exists($fileName,$this->jobs)) {
      $job = $this->jobs[$fileName];
    }

    $this->currentJob = $job;

    return $job;
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
