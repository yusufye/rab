<!-- Add Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body p-md-0">
        <div class="text-center mb-4">
          <h3 class="role-title mb-2 pb-0">Edit Role</h3>
          <p>Set role permissions</p>
        </div>
        <!-- Add role form -->
        <form id="addRoleForm" class="row g-3" action="/edit/role" method="POST">
          @csrf
          <div class="col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="hidden" id="role_id" name="role_id" class="form-control" readonly/>
            </div>
            <div class="form-floating form-floating-outline">
              <input type="text" id="modalRoleNameEdit" name="modalRoleNameEdit" class="form-control" required placeholder="Enter a role name" tabindex="-1" />
              <label for="modalRoleNameEdit">Role Name</label>
            </div>
          </div>
          <div class="col-12">
            <h5>Role Permissions</h5>
            <!-- Permission table -->
            <div class="table-responsive">
              <table class="table table-flush-spacing">
                <tbody>
                  <tr>
                    <td class="text-nowrap fw-medium">Administrator Access <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Allows a full access to the system"></i></td>
                    <td>
                      <div class="form-check">
                        <input class="form-check-input selectAllCheckboxEditRole" type="checkbox" id="selectAll" />
                        <label class="form-check-label" for="selectAll">
                          Select All
                        </label>
                      </div>
                    </td>
                  </tr>
                  @foreach ($permissions as $p)
                  <tr class="permission-row">
                  <td class="text-nowrap fw-medium">{{$p['name']}}</td>
                      <td>
                          <div class="d-flex">
                              <div class="form-check me-3 me-lg-5">
                                  <input class="form-check-input" value="create_{{ strtolower(str_replace(' ', '_', $p['permission_name'])) }}" name="create_permission[]" type="checkbox" id="userManagementRead" />
                                  <label class="form-check-label">
                                      Create
                                  </label>
                              </div>
                              <div class="form-check me-3 me-lg-5">
                                  <input class="form-check-input" value="read_{{ strtolower(str_replace(' ', '_', $p['permission_name'])) }}" name="read_permission[]" type="checkbox" id="userManagementWrite" />
                                  <label class="form-check-label">
                                      Read
                                  </label>
                              </div>
                              <div class="form-check me-3 me-lg-5">
                                  <input class="form-check-input" value="update_{{ strtolower(str_replace(' ', '_', $p['permission_name'])) }}" name="update_permission[]" type="checkbox" id="userManagementCreate" />
                                  <label class="form-check-label">
                                      Update
                                  </label>
                              </div>
                              <div class="form-check">
                                  <input class="form-check-input" value="delete_{{ strtolower(str_replace(' ', '_', $p['permission_name'])) }}" name="delete_permission[]" type="checkbox" id="userManagementCreate" />
                                  <label class="form-check-label">
                                      Delete
                                  </label>
                              </div>
                          </div>
                      </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- Permission table -->
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
        <!--/ Add role form -->
      </div>
    </div>
  </div>
</div>
<!--/ Add Role Modal -->

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var editRoleModal = document.getElementById('editRoleModal');    
    editRoleModal.addEventListener('show.bs.modal', function (event) {
        var checkboxes = editRoleModal.querySelectorAll('.form-check-input');
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = false;
        });
    });
    editRoleModal.addEventListener('hidden.bs.modal', function (event) {
        var checkboxes = editRoleModal.querySelectorAll('.form-check-input');
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = false;
        });
    });
});
</script>