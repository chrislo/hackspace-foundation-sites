<?php
class Box extends fActiveRecord {
    public function getAll() {
        return fRecordSet::build(
            'Box'
        );
    }

    public function getAvailable() {
        return fRecordSet::build(
            'Box',
            array('owned=' => 0,
                  'active=' => 1)
        );
    }

    public function isOwned() {
        if ($this->getOwnerId() and $this->getOwned and $this->getActive) {
            return True;
        }
        return False;
    }

    public function getOwner() {
        $owner = new User(array('id' => $this->getOwnerId()));
        return $owner;
    }
    
    public function getCreator() {
        $creator = new User(array('id' => $this->getCreatorId()));
        return $creator;
    }
}
