<?php
class TechnologyExperience extends DataObject{
  private static $db = array(
    'Title' => 'Varchar(255)',
    'InfoLink' => 'Varchar(255)'
  );

  public function forAPI(){
    return array(
      'title' => $this->Title,
      'infoLink' => $this->InfoLink
    );
  }
}
