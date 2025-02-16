<!-- Add Role Modal -->
<div class="modal fade" id="add-item-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">    
    <div class="modal-content">
            <div class="modal-header">
                  <h4 class="modal-title">Item</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <div class="modal-body">

                <!-- type form
                 1: create
                 0: edit
                -->                 
                <input type="hidden" id="type_form_item" name="type_form_item" readonly>
                <input type="hidden" id="order_title_id_item" name="order_title_id_item" readonly>
                <input type="hidden" id="order_item_id" name="order_item_id" readonly>

                <div class="row mb-4">
                    <div class="col">
                        <label id="order_label_mak_item"></label>
                        <br>
                        <label id="order_label_title"></label>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                        <input type="text" id="order_item" name="order_item" maxlength="50" class="form-control" placeholder="Item">
                        <label for="order_item" class="required">{{ __('Item') }}</label>
                    </div>
                    
                </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <input type="number" id="order_item_qty_1" name="order_item_qty_1" class="form-control" placeholder="Qty 1">
                            <label for="order_item_qty_1">{{ __('Qty 1') }}</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <input type="text" id="order_item_unit_1" name="order_item_unit_1" maxlength="10" class="form-control" placeholder="Unit 1">
                            <label for="order_item_unit_1">{{ __('Unit 1') }}</label>
                        </div>                
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <input type="number" id="order_item_qty_2" name="order_item_qty_2" class="form-control" placeholder="Qty 2">
                            <label for="order_item_qty_2">{{ __('Qty 2') }}</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <input type="text" id="order_item_unit_2" name="order_item_unit_2" maxlength="10" class="form-control" placeholder="Unit 2">
                            <label for="order_item_unit_2">{{ __('Unit 2') }}</label>
                        </div>                
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <input type="number" id="order_item_qty_3" name="order_item_qty_3" class="form-control" placeholder="Qty 3">
                            <label for="order_item_qty_3">{{ __('Qty 3') }}</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <input type="text" id="order_item_unit_3" name="order_item_unit_3" maxlength="10" class="form-control" placeholder="Unit 2">
                            <label for="order_item_unit_3">{{ __('Unit 3') }}</label>
                        </div>                
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <input type="number" id="qty_total" name="qty_total" class="form-control readonly" placeholder="Total Qty" readonly>
                            <label for="order_item_qty_2">{{ __('Total Qty') }}</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <input type="text" id="qty_unit" name="qty_unit" maxlength="10" class="form-control" placeholder="Unit">
                            <label for="order_item_qty_2">{{ __('Unit') }}</label>
                        </div>                
                    </div>
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <input type="text" id="price_unit" name="price_unit" class="form-control format-currency" placeholder="Price Unit">
                            <label for="price_unit">{{ __('Price Unit') }}</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col">
                        <div class="form-floating form-floating-outline mb-4">
                            <input type="text" id="total_price" name="total_price" maxlength="10" class="form-control readonly  format-currency" placeholder="Total Price" readonly>
                            <label for="order_item_qty_2">{{ __('Total Price') }}</label>
                        </div>                
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="button-save-item">Save</button>
                </div>
        </div>
    </div>
  </div>
</div>
