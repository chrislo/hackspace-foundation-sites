<?php
class Box extends fActiveRecord {
    public function getAvailableBoxes() {
        return fRecordSet::build(
            'Box',
            array('user_id=' => NULL)
        );
    }
}
