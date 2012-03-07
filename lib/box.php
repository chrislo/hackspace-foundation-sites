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
    
    public function getLocationName() {
        global $db;
        $result = $db->query("SELECT id, name FROM storage_locations
            WHERE id = %s", $this->getLocationId());
        if ($result->countReturnedRows() > 0) {
            $res = $result->fetchRow();
            return $res['name'];
        }
        return NULL;
    }
}
