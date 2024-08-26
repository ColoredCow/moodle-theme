<div class="">
    <div class="col-auto pt-1">
        <label for="username" class="col-form-label control-label">Username</label>
    </div>
    <div class="col-7">
        <input type="text" class="form-control" name="username" id="username" value="<?php echo $user->username; ?>" required>
        <div class="invalid-feedback">
            - Please provide a valid input.
        </div>
    </div>
</div>
<div class="">
    <div class="col-auto pt-1">
        <label for="email" class="col-form-label control-label">Email</label>
    </div>
    <div class="col-7">
        <input type="email" class="form-control" name="email"  value="<?php echo $user->email; ?>" id="email" required>
        <div class="invalid-feedback">
            - Please provide a valid input.
        </div>
    </div>
</div>
<div class="">
    <div class="col-auto pt-1">
        <label for="password" class="col-form-label control-label">Password</label>
    </div>
    <div class="col-7">
        <input type="password" class="form-control" name="password" id="password" value="">
        <small>Please leave empty if don't want to change the password</small>
        <div class="invalid-feedback">
            - Please provide a valid input.
        </div>
    </div>
</div>
<div class="">
    <div class="col-auto pt-1">
        <label for="firstname" class="col-form-label control-label">First Name</label>
    </div>
    <div class="col-7">
        <input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo $user->firstname; ?>" required>
        <div class="invalid-feedback">
            - Please provide a valid input.
        </div>
    </div>
</div>
<div class="">
    <div class="col-auto pt-1">
        <label for="lastname" class="col-form-label control-label">Last Name</label>
    </div>
    <div class="col-7">
        <input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo $user->lastname; ?>" required>
        <div class="invalid-feedback">
            - Please provide a valid input.
        </div>
    </div>
</div>