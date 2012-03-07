<?php
class Storage_Location extends fActiveRecord {
    public function getAvailable($user_id) {
        global $db;
        $result = $db->query("
            SELECT storage_locations.id, storage_locations.name, boxes.owner_id FROM storage_locations 
                LEFT OUTER JOIN boxes on storage_locations.id == boxes.location_id
                where 
                    (storage_locations.id not in (SELECT location_id FROM boxes where owned == 1 and location_id != '')
                    or storage_locations.id in (SELECT location_id FROM boxes where owner_id == %s));
        ", $user_id);
        return $result;
    }
}