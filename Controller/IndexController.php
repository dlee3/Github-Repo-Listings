<?php



namespace Controller;


use \Model\Github;


class IndexController extends SlimController
{

    // This gathers info and displays the home page.
	public function index ()
	{
        $repos = [];
        $totalRepos = 'who knows how many';

        // All of out repo logic is in the Github model
        // Get the repo info
        $git = new Github();
        $result = $git->getRepos();

        // If it succeed, gather the infor to display it
        if ( $result['status'] == 'success' ) {
            $repos = $result['items'];
            $totalRepos = $result['total'];
        }

        // Render the home page
        $this->app->render('home/index.php', [
            'title' => \Model\Config::$instance->app->name,
            'total' => $totalRepos,
            'repos' => $repos
        ]);
	}


}

