<?php

$userObj = new WIUser();

$users = $userObj->getUsers();
$roles = $userObj->getRoles();

?>

<aside class="right-side">

<div class="wi-admin-header">

<h2>Users</h2>

<p>Manage system users and roles.</p>

</div>

<section class="content wi-users-shell">

<div class="wi-admin-panel">

<div class="wi-section-head">

<h3>User Manager</h3>

</div>

<div class="wi-users-table-wrap">

<table class="table table-striped wi-users-table">

<thead>

<tr>

<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Name</th>
<th>Role</th>
<th>Registered</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php foreach($users as $user): ?>

<tr id="user-row-<?php echo (int)$user['id']; ?>">

<td><?php echo (int)$user['id']; ?></td>

<td><?php echo htmlspecialchars((string)$user['username']); ?></td>

<td><?php echo htmlspecialchars((string)$user['email']); ?></td>

<td>

<?php echo htmlspecialchars((string)$user['first_name']); ?>

<?php echo htmlspecialchars((string)$user['last_name']); ?>

</td>

<td>

<select

class="user-role-select"

data-user-id="<?php echo (int)$user['id']; ?>"

>

<?php foreach($roles as $role): ?>

<option

value="<?php echo (int)$role['role_id']; ?>"

<?php if($role['role'] === $user['role']) echo 'selected'; ?>

>

<?php echo htmlspecialchars((string)$role['role']); ?>

</option>

<?php endforeach; ?>

</select>

</td>

<td><?php echo htmlspecialchars((string)$user['registered']); ?></td>

<td>

<button

class="btn btn-danger btn-sm"

onclick="WIUsers.deleteUser(<?php echo (int)$user['id']; ?>)"

>

Delete

</button>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</section>

<script src="WICore/WIJ/WICore.js"></script>
<script src="WICore/WIJ/WIUsers.js"></script>

</aside>