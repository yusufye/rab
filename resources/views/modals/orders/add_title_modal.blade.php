<!-- Add Role Modal -->
<div class="modal fade" id="add-title-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">    
    <div class="modal-content">
            <div class="modal-header">
                  <h4 class="modal-title">Title</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <div class="modal-body">

                <!-- type form
                 1: create
                 0: edit
                -->                 
                <input type="hidden" id="type_form_title" name="type_form_title" readonly>
                <input type="hidden" id="order_title_mak_id" name="order_title_mak_id" readonly>
                <input type="hidden" id="order_title_id" name="order_title_id" readonly>

                <div class="row mb-4">
                    <div class="col">
                        <label id="order_label_mak"></label>
                    </div>
                </div>
                <div class="row">
                    
                <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <textarea type="text" id="order_title" name="order_title" class="form-control"></textarea>
                        <label for="order_title" class="required">{{ __('Title') }}</label>
                    </div>
                </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="button-save-title">Save</button>
                </div>
        </div>
    </div>
  </div>
</div>
