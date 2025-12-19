<div class="row">
    <div class="col-md-12">
        <div class="cd-header">
            <div class="media">
				<div class="party_image_view">
				     <img src="<?=base_url()?>assets/uploads/party/user_default.png" alt="user" class="rounded-circle thumb-sm party_image">
                </div>
                <div class="media-body">
                    <div class="row">
                        <h6 class="m-0"><?= $partyData->party_name ?></h6>
                        <p class="mb-0 lastSeen">Welcomes You</p>
                    </div>
                </div>
            </div>
            <hr>
        </div>
        <div class="cd-body">
            <div class="cd-detail slimscroll">
			    <div class="activity">
                    <?php echo $salesLog; ?>
                </div>
            </div>
        </div>
    </div>
</div>