<?php $pageTitle = 'My Profile'; require __DIR__ . '/../layout/header.php'; ?>
<?php $social = json_decode($user['social_links'] ?? '{}', true) ?? []; ?>

<div class="page-header">
    <h1 class="page-title">My Profile</h1>
    <p class="page-sub">Update your author information and account settings.</p>
</div>

<div class="two-col">
    <!-- Profile form -->
    <div class="card">
        <div class="card-header"><h3>Profile Information</h3></div>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=author_profile" enctype="multipart/form-data">
            <input type="hidden" name="_route" value="author_profile_update">

            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
                <?php if ($user['profile_pic']): ?>
                <img src="<?= UPLOAD_URL . htmlspecialchars($user['profile_pic']) ?>" class="profile-pic-large" alt="avatar">
                <?php else: ?>
                <div class="profile-pic-large profile-pic-placeholder"><?= strtoupper(substr($user['name'],0,1)) ?></div>
                <?php endif; ?>
                <div>
                    <label class="btn btn-sm btn-outline" for="picUpload">Change Photo</label>
                    <input type="file" name="profile_pic" id="picUpload" accept="image/*" style="display:none">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Display Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Bio</label>
                <textarea name="bio" class="form-control" rows="4" placeholder="Tell readers about yourself…"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Twitter / X handle</label>
                <input type="text" name="twitter" class="form-control" value="<?= htmlspecialchars($social['twitter'] ?? '') ?>" placeholder="@yourhandle">
            </div>
            <div class="form-group">
                <label class="form-label">LinkedIn</label>
                <input type="text" name="linkedin" class="form-control" value="<?= htmlspecialchars($social['linkedin'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">GitHub</label>
                <input type="text" name="github" class="form-control" value="<?= htmlspecialchars($social['github'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save Profile</button>
        </form>
    </div>

    <div>
        <!-- Change password -->
        <div class="card">
            <div class="card-header"><h3>Change Password</h3></div>
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=author_profile&action=changepassword">
                <input type="hidden" name="_route" value="author_profile_changepassword">
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>

        <!-- Followers -->
        <div class="card" style="margin-top:1rem;">
            <div class="card-header">
                <h3>Followers</h3>
                <span class="card-link"><?= count($followers) ?> total</span>
            </div>
            <?php if (empty($followers)): ?>
                <p class="empty-msg">No followers yet.</p>
            <?php else: ?>
            <div class="follower-list">
                <?php foreach (array_slice($followers,0,10) as $f): ?>
                <div class="follower-item">
                    <div class="comment-avatar"><?= strtoupper(substr($f['name'],0,1)) ?></div>
                    <div>
                        <div><?= htmlspecialchars($f['name']) ?></div>
                        <div class="comment-time">@<?= htmlspecialchars($f['username']) ?> · <?= timeAgo($f['created_at']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (count($followers) > 10): ?>
                <p class="empty-msg">…and <?= count($followers)-10 ?> more</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Account info -->
        <div class="card" style="margin-top:1rem;">
            <div class="card-header"><h3>Account Info</h3></div>
            <div style="display:grid;gap:.5rem;font-size:.9rem;">
                <div><strong>Username:</strong> @<?= htmlspecialchars($user['username']) ?></div>
                <div><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></div>
                <div><strong>Role:</strong> Author</div>
                <div><strong>Member since:</strong> <?= formatDate($user['created_at']) ?></div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
