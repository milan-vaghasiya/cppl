<?php
class Migration extends MY_Controller{
    public function __construct(){
        parent::__construct();
    }

    /*public function addColumnInTable(){
        $result = $this->db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'jaracrm' AND TABLE_NAME NOT IN ( SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'updated_by' AND TABLE_SCHEMA = 'jaracrm' )")->result();

        foreach($result as $row):
                $this->db->query("ALTER TABLE ".$row->TABLE_NAME." ADD `updated_by` INT NOT NULL DEFAULT '0' AFTER `created_at`;");
        endforeach;

        echo "success";exit;
    }*/

	/*** By : NYN @09.03.2024 Migration/migrateFgData ***/    
    public function migrateFgData(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $result = $this->db->get('item_master')->result();
            
            foreach($result as $row):
            
                $this->db->reset_query();
                $udfData = [
                    'item_id'=>$row->id,
                    'f1'=>trim($row->remark)
                ];
                $this->db->insert('item_udf',$udfData);
                
                $itData = array();
                $itData['remark'] = NULL;
                
                $this->db->reset_query();
                $this->db->where('id',$row->id);
                $this->db->update('item_master',$itData);
            endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    /*****
     * Mansee
     * Item Category Migrate From old database
     * 17-04-2024
     */
    public function migrateCatData(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $this->db->select('item_category_backup.*');
            $this->db->where("is_delete",0);
            $result = $this->db->get('item_category_backup')->result();
            $count = 0;
            foreach($result as $row):
            
                $this->db->reset_query();
                $catData = [
                    'id'=>$row->id,
                    'category_level'=>$row->category_level,
                    'category_name'=>$row->category_name,
                    'category_type'=>$row->category_type,
                    'final_category'=>$row->final_category,
                    'ref_id'=>$row->ref_id,
                    'created_by'=>$row->created_by,
                    'created_at'=>$row->created_at,
                ];
                $this->db->insert('item_category',$catData);
                $count++;
            endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Migration Successfully.".$count;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    public function migrateItemData(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $this->db->select('item_master_backup.*,item_udf_backup.f1,unit_master.unit_name as unm');
            $this->db->join('item_udf_backup','item_udf_backup.item_id = item_master_backup.id','left');
            $this->db->join('unit_master','unit_master.id = item_master_backup.unit_id','left');
            $this->db->where("item_master_backup.is_delete",0);
            $result = $this->db->get('item_master_backup')->result();
            $count = 0;
            foreach($result as $row):
            
                $this->db->reset_query();
                $itemData = [
                    'id'=>'',
                    'item_name'=>$row->item_name,
                    'item_code'=>$row->item_code,
                    'hsn_code'=>$row->hsn_code,
                    'category_id '=>$row->category_id,
                    'unit_name'=>$row->unm,
                    'price'=>$row->price,
                    'mrp'=>$row->mrp,
                    'primary_packing'=>$row->primary_packing,
                    'master_packing'=>$row->master_packing,
                    'gst_per'=>$row->gst_per,
                    'wt_pcs'=>$row->wt_pcs,
                    'img_file'=>$row->drawing_file,
                    'created_by'=>$row->created_by,
                    'created_at'=>$row->created_at,
                ];
                $this->db->insert('item_master',$itemData);
                $item_id = $this->db->insert_id();
                $udfData = [
                    'item_id'=> $item_id,
                    'f1'=>$row->f1
                ];
                $this->db->insert('item_udf',$udfData);
                $count++;
            endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Migration Successfully.".$count;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
}
?>