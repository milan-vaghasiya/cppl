
<ul class="dz-list message-list">
    <?php
    $wa_text = urlencode('Hello');
    if(!empty($dataRow->contact_person) || !empty($dataRow->contact_phone)){
        $no ="'tel: ".$dataRow->contact_phone."'";
        $wa_number = (!empty($dataRow->contact_phone) ? str_replace('-','',str_replace('+','',str_replace(' ','',$dataRow->contact_phone))) : '');
        ?>
        <li class="istItem ">
            <a href="javascript:void(0)" class="position-relative">
                <div class="media-content">
                    <div>
                        <h6 class="name"><?=$dataRow->contact_person?></h6>
                        <span class="name"><?=$dataRow->contact_phone?></span>
                    </div>
                </div>
            </a>
                <div class="left-content">
                    <div class="d-flex mt-2">
                        <a role="button" href="https://wa.me/<?=$wa_number?>/?text='<?=$wa_text?>" target="_blank" class="text-left p-1" ><i class="fab fa-whatsapp text-success font-20" ></i></a>
                        <a role="button" href="javascript:void(0)" class=" text-left p-1" onclick="document.location.href = <?=$no?>"><i class="fas fa-phone text-primary font-20"  ></i></a>
                    </div>
                </div>
        </li>
        <?php
    }
    
    if(!empty($dataRow->cp_1) || !empty($dataRow->cn_1)){
        $no1 ="'tel: ".$dataRow->cn_1."'";
        $wa_number1 = (!empty($dataRow->cn_1) ? str_replace('-','',str_replace('+','',str_replace(' ','',$dataRow->cn_1))) : '');
        ?>
        <li class="istItem ">
            <a href="javascript:void(0)" class="position-relative">
                <div class="media-content">
                    <div>
                        <h6 class="name"><?=$dataRow->cp_1?></h6>
                        <span class="name"><?=$dataRow->cn_1?></span>
                    </div>
                </div>
            </a>
                <div class="left-content">
                    <div class="d-flex mt-2">
                        <a role="button" href="https://wa.me/<?=$wa_number1?>/?text='<?=$wa_text?>" target="_blank" class="text-left p-1" ><i class="fab fa-whatsapp text-success font-20" ></i></a>
                        <a role="button" href="javascript:void(0)" class=" text-left p-1" onclick="document.location.href = <?=$no1?>"><i class="fas fa-phone text-primary font-20"  ></i></a>
                    </div>
                </div>
        </li>
        <?php
    }
    if(!empty($dataRow->cp_2) || !empty($dataRow->cn_2)){
        $no2 ="'tel: ".$dataRow->cn_2."'";
        $wa_number2 = (!empty($dataRow->cn_2) ? str_replace('-','',str_replace('+','',str_replace(' ','',$dataRow->cn_2))) : '');
        ?>
        <li class="istItem ">
            <a href="javascript:void(0)" class="position-relative">
                <div class="media-content">
                    <div>
                        <h6 class="name"><?=$dataRow->cp_2?></h6>
                        <span class="name"><?=$dataRow->cn_2?></span>
                    </div>
                </div>
            </a>
                <div class="left-content">
                    <div class="d-flex mt-2">
                        <a role="button" href="https://wa.me/<?=$wa_number2?>/?text='<?=$wa_text?>" target="_blank" class="text-left p-1" ><i class="fab fa-whatsapp text-success font-20" ></i></a>
                        <a role="button" href="javascript:void(0)" class=" text-left p-1" onclick="document.location.href = <?=$no2?>"><i class="fas fa-phone text-primary font-20"  ></i></a>
                    </div>
                </div>
        </li>
        <?php
    }
    if(!empty($dataRow->cp_3) || !empty($dataRow->cn_3)){
        $no3 ="'tel: ".$dataRow->cn_3."'";
        $wa_number3 = (!empty($dataRow->cn_3) ? str_replace('-','',str_replace('+','',str_replace(' ','',$dataRow->cn_3))) : '');
        ?>
        <li class="istItem ">
            <a href="javascript:void(0)" class="position-relative">
                <div class="media-content">
                    <div>
                        <h6 class="name"><?=$dataRow->cp_3?></h6>
                        <span class="name"><?=$dataRow->cn_3?></span>
                    </div>
                </div>
            </a>
                <div class="left-content">
                    <div class="d-flex mt-2">
                        <a role="button" href="https://wa.me/<?=$wa_number3?>/?text='<?=$wa_text?>" target="_blank" class="text-left p-1" ><i class="fab fa-whatsapp text-success font-20" ></i></a>
                        <a role="button" href="javascript:void(0)" class=" text-left p-1" onclick="document.location.href = <?=$no3 ?>"><i class="fas fa-phone text-primary font-20"  ></i></a>
                    </div>
                </div>
        </li>
        <?php
    }
    ?>       
    
</ul>
    