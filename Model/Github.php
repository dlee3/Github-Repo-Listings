<?php


namespace Model;




class Github
{
    // Having a base url will allow us to easily expand our searches in the future
    private $baseUrl = 'https://api.github.com/search/repositories?q=language:%s&sort=%s&order=%s%s';

    public $total = null;
    public $items = null;
    public $error = null;



    // Public function used to retrieve repo information
    public function getRepos()
    {
        $success = true;

        // Check the DB to see if the repos already exist.
        $stmt = Db::$pdo->query("SELECT updated FROM config where item = 'lastRepoUpdate' LIMIT 1");
        $logExists = $stmt->rowCount();
        $result = $stmt->fetch();

        // Use PHP time instead of MySQL so that we ensure the
        // timezone is consistent throughout the app.
        $nowDt = new \DateTimeImmutable('now');
        $now = $nowDt->format('Y-m-d H:i:s');

        // If there is no log entry
        if (!$logExists) {

            // Update the repos
            $success = $this->saveUpdateRepos();
            if ($success) {
                // If they were saved properly,
                // insert a new entry for the time for the last repo update
                $stmt = Db::$pdo->prepare("INSERT INTO config
                    (item, updated)
                    VALUES ('lastRepoUpdate',  :now )");
                $stmt->bindParam(':now', $now);
                $stmt->execute();
            }

        } else { // There is a log entry

            // We're only going to update the repos every 15 min.
            // So lets check to see if they need to be updated.
            $lastUpdateDt = new \DateTime($result['updated']);
            // go back 15 min
            $timeToUpdateDt = $nowDt->modify('-15 minutes');

            // Check to see if we need to update
            if ($lastUpdateDt < $timeToUpdateDt) {

                // Update the repos
                $success = $this->saveUpdateRepos();
                if ($success) {
                    // If they were updated properly,
                    // Update the time for the last repo update
                    $stmt = Db::$pdo->prepare("UPDATE config
                    SET updated = :now
                    WHERE item = 'lastRepoUpdate' ");
                    $stmt->bindParam(':now', $now);
                    $stmt->execute();
                }
            }
        }

        // If there was a problem, return error
        if (!$success) {
            return [
                'status'  => 'error',
                'message' => $this->error
            ];
        }

        // Get the total count from the db
        $stmt = Db::$pdo->prepare("SELECT amount FROM config
                                   WHERE item = 'totalRepos' LIMIT 1 ");
        $stmt->execute();

        $total = $stmt->fetch();
        $total = $total['amount'];


        // Get the repo info from the db
        $stmt = Db::$pdo->prepare("SELECT * FROM repos");
        $stmt->execute();

        $items = $stmt->fetchAll();


        return [
            'status' => 'success',
            'total'  => $total,
            'items'  => $items
        ];
    }


    ////// Start Private Function //////

    // Either saves or updates the repos
    private function saveUpdateRepos()
    {
        $success = false;

        // Get the repos from Github
        $result = $this->getPHPRepos();

        // If something was returned, save it
        if ($result) {
            $success = $this->saveRepos();
        }

        return $success;
    }


    // This makes the curl connection to Github to retrieve PHP repos.
    // The default was to return 30 results.
    // I extended that to 100 so that we have more data.
    private function getPHPRepos()
    {
        $queryUrl = sprintf(
            $this->baseUrl,
            'php',
            'stars',
            'desc',
            '&per_page=100' // We want more than 30 results
        );

        // create the curl connection
        $ch = curl_init($queryUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: ' . Config::$instance->app->name]);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $result = curl_exec($ch);
        curl_close($ch);

        // decode the data
        $res = json_decode($result);

        // We want to make sure we got something back. If there were no
        // results then return false.
        if (!isset($res->items) || empty($res->items)) {
            return false;
        }

        $this->items = $res->items;
        $this->total = $res->total_count;


        return true;
    }


    // Save Repo Info
    private function saveRepos()
    {
        $success = true;

        // Cycle through the repos and save or update the info.
        // Using PDO and binding parameters so
        // that the entries are properly escaped.
        foreach ($this->items as $item) {
            // Check the DB to see if the repos already exist.
            $stmt = Db::$pdo->prepare("SELECT * FROM repos WHERE repo_id = ?");
            $stmt->execute([$item->id]);

            // We don't want to create duplicate entries
            if (!$stmt->fetch()) {
                // If it's new add it!
                $repoStmt = Db::$pdo->prepare("INSERT INTO repos
                            (repo_id, name, url, description, stars, pushed, created)
                            VALUES (:repo_id, :name, :url, :description, :stars, :pushed, :created)");
            } else {
                // We already have it, so let's update it with the latest info.
                $repoStmt = Db::$pdo->prepare("UPDATE repos SET
                            name = :name, url = :url, description = :description,
                            stars = :stars, pushed = :pushed, created = :created
                            WHERE repo_id = :repo_id");
            }

            // Bind all of the parameters and save the data.
            $repoStmt->bindParam(':repo_id', $item->id, \PDO::PARAM_INT);
            $repoStmt->bindParam(':name', $item->full_name, \PDO::PARAM_STR);
            $repoStmt->bindParam(':url', $item->html_url, \PDO::PARAM_STR);
            $repoStmt->bindParam(':description', $item->description, \PDO::PARAM_STR);
            $repoStmt->bindParam(':stars', $item->stargazers_count, \PDO::PARAM_INT);
            $repoStmt->bindParam(':pushed', $item->pushed_at);
            $repoStmt->bindParam(':created', $item->created_at);
            $savedRepos = $repoStmt->execute();


            if (!$savedRepos) $success = false;

        }

        // Use PHP time instead of MySQL so that we ensure the
        // timezone is consistent throughout the app.
        $nowDt = new \DateTimeImmutable('now');
        $now = $nowDt->format('Y-m-d H:i:s');

        // Let's see if we've save the total before
        $stmt = Db::$pdo->prepare("SELECT * FROM config WHERE item = 'totalRepos'");
        $stmt->execute();

        // We don't want to create duplicate entries
        if (!$stmt->fetch()) {
            $totalStmt = Db::$pdo->prepare("INSERT INTO config
                            (item, amount, updated)
                            VALUES ('totalRepos', :total, :now)");
        } else {
            $totalStmt = Db::$pdo->prepare("UPDATE config SET
                            amount = :total, updated = :now 
                            WHERE item = 'totalRepos'");
        }

        $totalStmt->bindParam(':total', $this->total, \PDO::PARAM_INT);
        $totalStmt->bindParam(':now', $now);
        $savedTotal = $totalStmt->execute();


        if (!$savedTotal) $success = false;


        return $success;
    }


}
