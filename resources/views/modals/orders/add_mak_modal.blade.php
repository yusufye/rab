<!-- Add Role Modal -->
<div class="modal fade" id="add-mak-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">    
    <div class="modal-content">
            <div class="modal-header">
                  <h4 class="modal-title">Mak</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <div class="modal-body">
                <input type="hidden" id="order_id" name="order_id" readonly>

                <!-- type form
                 1: create
                 0: edit
                -->                 
                <input type="hidden" id="type_form" name="type_form" readonly>
                <input type="hidden" id="order_mak_id" name="order_mak_id" readonly>

                <div class="row">
                    <div class="col-6">
                        <div class="form-floating form-floating-outline mb-4">
                            <select id="mak" required class="select2 form-select" data-required="Mak" name="mak"
                                data-placeholder="{{ __('Select Mak') }}">
                                <option value="">{{ __('Select Mak') }}</option>
                                @forelse($maks as $mak)
                                    <option value="{{$mak->id}}">{{$mak->mak_code}} - {{$mak->mak_name}}</option>
                                @empty
                                @endforelse
                            </select>
                            <label for="mak" class="required">{{ __('Mak') }}</label>
                            </div>
                    </div>
                    <div class="col-2">
                             <label class="switch">
                                <input type="checkbox" id="is_split" name="is_split" class="switch-input" />
                                <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                                </span>
                                <span class="switch-label">Split</span>
                            </label>
                    </div>

                    <div class="col-4" style="display: none;" id="div_split_to">
                        <div class="form-floating form-floating-outline mb-4">
                            <select id="split_to" class="select2 form-select" name="split_to"
                                data-placeholder="{{ __('Select Split To') }}">
                                <option value="">{{ __('Select Split To') }}</option>
                                @forelse($divisions_by_order_header as $div)
                                    <option value="{{$div->id}}">{{$div->division_name}}</option>
                                @empty
                                @endforelse
                            </select>
                            <label for="split_to" class="required">{{ __('Split To') }}</label>
                    </div>
                </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="button-save-mak">Save</button>
                </div>
        </div>
    </div>
  </div>
</div>
