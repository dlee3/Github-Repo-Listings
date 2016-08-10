<?php include_once __DIR__ . '/../includes/header.php'; ?>

<div class="row title-container">
    <h1><?= $title ?></h1>
    <p class="col-xs-11 col-xs-offet-1">There are currently <?= $total ?> PHP repos on Github.</p>
</div>

<div class="row">
    <div id="repoList" class="col-md-6 content-container">
        <h2>Top 100 Repos</h2>
        <hr>
        <table id="repoTable" class="table" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="small"><strong>Repository</strong></th>
                    <th class="small"><strong>Stars</strong></th>
                </tr>
            </thead>
            <tbody>

        <?php foreach ($repos as $repo) : ?>
            <tr class="clickable-row" data-id="<?= $repo['repo_id'] ?>">
                    <td class="small">
                        <span class="glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span>
                        <?= $repo['name'] ?>
                    </td>
                    <td class="small">
                        <span class="glyphicon glyphicon-star star-icon" aria-hidden="true"></span>
                        <?= $repo['stars'] ?>
                    </td>
            </tr>
        <?php endforeach; ?>
            </tbody>
        </table>
    </div> <!-- End list of repos -->

    <div id="repoDetails" class="col-md-6 content-container">
        <h2>Repo Details</h2>
        <hr>
        <?php $firstRow = true;
            foreach ($repos as $repo) : ?>
            <div id="repo<?= $repo['repo_id'] ?>" class="<?php echo !$firstRow ? 'hidden' : '' ?> repo-container">
                <div class="row">
                    <h4 class="col-xs-4">Name:</h4>
                    <h4 class="col-xs-8"><?= $repo['name'] ?></h4>
                </div>
                <div class="row">
                    <p class="col-xs-4">Description:</p>
                    <p class="col-xs-8"><?= $repo['description'] ?></h4>
                </div>
                <a href="<?= $repo['url'] ?>" target="_blank">
                    <div class="row">
                        <p class="col-xs-4">Url:</p>
                        <p class="col-xs-8"><?= $repo['url'] ?></p>
                    </div>
                </a>
                <div class="row">
                    <p class="col-xs-4">Stars:</>
                    <p class="col-xs-8">
                        <?= $repo['stars'] ?>
                        <span class="glyphicon glyphicon-star star-icon" aria-hidden="true"></span>
                    </p>
                </div>
                <div class="row">
                    <p class="col-xs-4">Last Pushed:</p>
                    <p class="col-xs-8"><?= date_format(date_create($repo['pushed']), 'M d, Y \a\t g:i:s a') ?></p>
                </div>
                <div class="row">
                    <p class="col-xs-4">Created:</p>
                    <p class="col-xs-8"><?= date_format(date_create($repo['created']), 'M d, Y \a\t g:i:s a') ?></p>
                </div>
                <div class="row row-btn">
                    <a class="btn btn-primary pull-right repo-btn"
                       href="<?= $repo['url'] ?>"
                       target="_blank">Check out this repo!</a>
                </div>
            </div>
        <?php $firstRow = false;
            endforeach; ?>
    </div> <!-- End Repo Details -->

</div>




<?php include_once __DIR__ . '/../includes/footer.php' ?>
