<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Company Name</th>
                        <td colspan="3"><?=(!empty($leadData->party_name) ? $leadData->party_name : '')?></td>
                    </tr>
                    <tr>
                        <th style="width:25%">Contact Person</th>
                        <td style="width:25%"><?=(!empty($leadData->contact_person) ? $leadData->contact_person : '')?></td>
                        <th style="width:25%">Contact No.</th>
                        <td style="width:25%"><?=(!empty($leadData->contact_phone) ? $leadData->contact_phone : '')?></td>
                    </tr>
                    <tr>
                        <th>Whatsapp No.</th>
                        <td><?=(!empty($leadData->whatsapp_no) ? $leadData->whatsapp_no : '')?></td>
                        <th>State</th>
                        <td><?=(!empty($leadData->state) ? $leadData->state : '')?></td>
                    </tr>
                    <tr>
                        <th>District</th>
                        <td><?=(!empty($leadData->district) ? $leadData->district : '')?></td>
                        <th>Taluka</th>
                        <td><?=(!empty($leadData->taluka) ? $leadData->taluka : '')?></td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td colspan="3"><?=(!empty($leadData->query_subject) ? $leadData->query_subject : '')?></td>
                    </tr>
                    <tr>
                        <th>Product</th>
                        <td colspan="3"><?=(!empty($leadData->query_product) ? $leadData->query_product : '')?></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td colspan="3"><?=(!empty($leadData->query_msg) ? $leadData->query_msg : '')?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</form>