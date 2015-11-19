<?php
class WorkExperience extends DataObject{
  private static $db = array(
    'Title' => 'Varchar(255)',
    'StartDate' => 'SS_Datetime',
    'EndDate' => 'SS_Datetime',
    'Content' => 'HTMLText'
  );

  private static $many_many = array(
    'ExperienceItems' => 'ExperienceItem'
  );

  public function forAPI(){
    return array(
      'title' => $this->Title,
      'startDate' => $this->obj('StartDate')->Rfc3339(),
      'endDate' => $this->obj('EndDate')->Rfc3339(),
      'content' => $this->Content
    );
  }
}
