<div id="createUserMessage" class="alert" style="display: none;"></div>
<form id="createUserForm" method="POST" class="create-user-form">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="form-group col-md-6">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group col-md-6">
            <label for="role">Role</label>
            <select class="form-control" id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="support">Support</option>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Create User</button>
</form> 