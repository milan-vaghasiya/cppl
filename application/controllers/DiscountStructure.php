<?php
class DiscountStructure extends MY_Controller{
    private $index = "discount_structure/index";
    private $form = "discount_structure/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Discount Structure";
		$this->data['headData']->controller = "discountStructure";
        $this->data['headData']->pageUrl = "discountStructure";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }
	
    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->configuration->getDiscountStructureDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getDiscountStructureData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStructure(){
        $this->data['catHtml'] = $this->getCategoryHtml();
        $this->data['masterList'] = $this->configuration->getDiscountData(['type'=>2,'group_by'=>'structure_name']);
        $this->load->view($this->form, $this->data);
    }

    public function getCategoryHtml($param = []){
        if(!empty($param['structure_name'])){
            $oldData = $this->configuration->getDiscountData(['structure_name'=>$param['structure_name']]);
        }
        $mainCategory = $this->item->getCategoryList(['ref_id'=>1]);
        $catHtml = "";
        if(!empty($mainCategory)){
            foreach($mainCategory as $row){
                $html="";
                $customWhere = "item_category.category_level	LIKE '".$row->category_level.".%' AND item_category.final_category=1 ";
                $subCategory = $this->item->getCategoryList(['customWhere'=>$customWhere]);
                $inputDisc = "";$inputType="";
                $html.='<div class="col-md-12 form-group"><table class="table jpExcelTable">';
                $html .='<tr class="bg-light"><th rowspan="2">Main Category</th>';
                foreach($subCategory as $cat){
                    $discount = "";$id="";$kg_price ="";

                    if(!empty($oldData)){
                        if(in_array($cat->id,array_column($oldData,'category_id'))){
                            $array_key = array_search($cat->id,array_column($oldData,'category_id'));
                        
                            $id = $oldData[$array_key]->id;
                            $discount = $oldData[$array_key]->discount;
                            $kg_price = $oldData[$array_key]->kg_price;
                        }
                        
                    }
                   
                    $html .='<th colspan="2" class="text-center">
                                <input type="hidden" name="category_id[]" value="'.$cat->id.'">'.$cat->category_name.'
                             </th>';
                    $inputDisc .= '<td>
                                    <input type="text" id="cat_discount'.$cat->id.'" name="cat_discount['.$cat->id.']" class="form-control floatOnly" value="'.$discount.'">
                                    <input type="hidden" name="id['.$cat->id.']" class="form-control" value="'.$id.'">
                                </td>
                                <td>
                                    <input type="text" id="kg_price'.$cat->id.'" name="kg_price['.$cat->id.']" class="form-control floatOnly" value="'.$kg_price.'"> 
                                </td>';

                    $inputType .= ' <td>Discount(%)</td>
                                    <td>K.G. Price</td>';
                }
                $html .='</tr>
                    <tr class="bg-light text-center">
                        '.$inputType.'
                    </tr>
                    <tr>
                        <th class="bg-light">'.$row->category_name.'</th>
                        '.$inputDisc.'
                    </tr>
                    </table></div>';
                 $catHtml .= $html;  
            }
        }
        return $catHtml;
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['structure_name'])){
			$errorMessage['structure_name'] = "Name is required.";
        }
        if($data['type'] == 1):
            if(empty($data['master_id'])){
                $errorMessage['master_id'] = "Master is required.";
            }

            foreach($data['category_id'] as $category_id){ 
                $discData = $this->configuration->getDiscountData(['type'=>2,'category_id'=>$category_id]);

                foreach($discData as $row):
                    if($data['cat_discount'][$category_id] > $row->discount):
                        $errorMessage['cat_discount'.$category_id] = "Invalid Discount.";
                    endif; 

                    if($data['kg_price'][$category_id] > $row->discount): 
                        $errorMessage['kg_price'.$category_id] = "Invalid Kg Price.";
                    endif; 
                endforeach;
            } 
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            
            $this->printJson($this->configuration->saveDiscountStructure($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] =$dataRow = $this->configuration->getDiscountData(['id'=>$data['id'],'single_row'=>1]);
        $this->data['catHtml'] = $this->getCategoryHtml(['structure_name'=>$dataRow->structure_name]);
        $this->data['masterList'] = $this->configuration->getDiscountData(['type'=>2,'group_by'=>'structure_name']);
        $this->load->view($this->form, $this->data);
    }
}
?>