<?php
namespace CameraLife\Views;
use CameraLife\Models as Models;
use CameraLife\Controllers as Controllers;

/**
 * Shows a welcome page, the "index" for the website
 *
 * @author William Entriken <cameralife@phor.net>
 * @copyright 2001-2014 William Entriken
 * @access public
 */
class MainPageView extends View
{
    /**
     * Either: rand, popular, unpopular, newest, newest-folders
     *
     * @var string
     * @access public
     */
    public $activeSection;

    /**
     * openGraphsForTop
     *
     * @var OpenGraph[]
     * @access public
     */
    public $openGraphsForTop;

    /**
     * folders
     *
     * @var Folder[]
     * @access public
     */
    public $folders;

    /**
     * tag collections
     *
     * @var TagCollection[]
     * @access public
     */
    public $tagCollections;
    
    public $adminUrl;

    public $rootOpenGraph;

    /**
     * Render the view to standard output
     *
     * @access public
     * @return void
     */
    public function render()
    {
//todo DONT USE  <code>/photos</code>, actually get the dir
        if (!count($this->openGraphsForTop)) {
        ?>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <h2 class="text-success"><i class="fa fa-check"></i> Camera Life <?= constant('CAMERALIFE_VERSION') ?> is installed!</h2>
                    <p><a class="btn btn-default btn-large" target="_blank" href="https://github.com/fulldecent/cameralife"><i
                    class="fa fa-star"></i> Star us on GitHub</a> to get important security updates</p>
                    <hr>                    
                    <p>
                        Add photos to your <code>/photos</code> directory or visit <a href="<?= htmlspecialchars($this->adminUrl) ?>">site administration</a> to point to your existing photo directory.
                    </p>
                </div>
            </div>
            <div class="container">
        <?php
        }
        ?>

        <div class="well">
            <ul class="nav nav-pills">
                <li <?= $this->activeSection == 'rand' ? 'class="active"' : '' ?>><a href="?section=rand">Random photos</a></li>
                <li <?= $this->activeSection == 'newest' ? 'class="active"' : '' ?>><a href="?section=newest">Newest photos</a></li>
                <li <?= $this->activeSection == 'newest-folders' ? 'class="active"' : '' ?>><a href="?section=newest-folders">Newest folders</a></li>
                <li <?= $this->activeSection == 'unpopular' ? 'class="active"' : '' ?>><a href="?section=unpopular">Unpopular</a></li>
            </ul>
            <div style="height: 170px" class="clipbox">
                <?php
                foreach ($this->openGraphsForTop as $resultOpenGraph) {
                    $htmlTitle = '';
                    if ($resultOpenGraph->title != 'unnamed') {
                        $htmlTitle = htmlentities($resultOpenGraph->title);
                    }

                    echo '<div class="l1" style="-moz-transform:rotate(' . rand(
                        -10,
                        10
                    ) . 'deg); -webkit-transform:rotate(' . rand(-10, 10) . 'deg)">';
                    echo '<a href="' . htmlspecialchars($resultOpenGraph->url) . '" class="l2">';
                    echo '<img alt="' . htmlspecialchars($resultOpenGraph->title) . '" src="' . htmlspecialchars(
                        $resultOpenGraph->image
                    ) . '" class="l3">';
                    if (isset($resultOpenGraph->imageWidth) && isset($resultOpenGraph->imageHeight)) {
                        echo '<div class="l4" style="width:' . ($resultOpenGraph->imageWidth) . 'px">' . $htmlTitle . '</div>';
                    } else {
                        echo '<div class="l4">' . $htmlTitle . '</div>';
                    }
                    echo '</a>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-7">
                <h2>Folders</h2>
                <table class="table">
                    <?php
                    foreach ($this->folders as $folder) {
//todo breaks mvc                        
#                        $folderCtrl = new Controllers\FolderController($folder->id);
                        $folderCtrl = $folder;
                        echo "<tr><td><h3><a href=\"" . htmlspecialchars($folderCtrl->url) . "\"> ";
                        echo htmlentities($folderCtrl->title) . "</a></h3>\n";

//todo huge hack
                        $folder = new Models\Folder($folderCtrl->title);
                        $folder->sort = 'rand';
                        $folder->setPage(0, 11);
                        $photos = $folder->getPhotos();
                        echo '<div style="height:80px" class="clipbox">';
                        foreach ($photos as $openGraph) {
//todo breaks mvc
                            $photoCtrl = new Controllers\PhotoController($openGraph->id);
                            echo '<div class="l1" style="-moz-transform:rotate(' . rand(
                                -10,
                                10
                            ) . 'deg); -webkit-transform:rotate(' . rand(-10, 10) . 'deg);">';
                            echo '<a class="minipolaroid" href="' . htmlspecialchars($photoCtrl->url) . '">';
                            echo '<img width="' . intval($photoCtrl->imageWidth / 2) . '" src="' . htmlspecialchars(
                                $photoCtrl->image
                            ) . '" alt="' . htmlentities($photoCtrl->title) . '" />';
                            echo '</a>';
                            echo '</div>';
                        }
                        echo "</div>\n";
                    }
                    echo "<tr><td><h3><a href=\"" . htmlspecialchars(
                        $this->rootOpenGraph->url
                    ) . "\">... show all folders</a></h3>";
                    ?>
                </table>
            </div>
            <div class="col-sm-5">
                <h2>Tag collections</h2>
                <table class="table">
                    <?php

                    foreach ($this->tagCollections as $tagCollection) {
                        $tCC = new Controllers\TagCollectionController($tagCollection->query);

                        echo "<tr><td><h3><a href=\"" . htmlentities($tCC->url) . "\"><i class=\"fa fa-tags\"></i> ";
                        echo htmlentities($tCC->title) . "</a></h3>\n";
                        $tagCollection->sort = 'rand';
                        $tagCollection->SetPage(0, 4);

                        echo '<div style=" overflow: hidden; white-space: nowrap; text-overflow: ellipsis; ">';
                        $count = 0;
                        foreach ($tagCollection->getTags() as $tag) {
                            $tC = new Controllers\TagController($tag->record['id']);
                            if ($count++) {
                                echo ", \n";
                            }
                            echo "<a href=\"" . htmlentities($tC->url) . "\"><i class=\"fa fa-tag\"></i> ";
                            echo htmlentities($tC->title) . "</a>";
                        }
                        echo ", ...</div>\n";
                    }
//todo fix url                    
                    echo "<tr><td><h3><a href=\"search.php&#63;albumhelp=1&amp;q=\">... create a featured tag</a></h3>";

                    ?>
                </table>
            </div>
        </div>
        <?php
    }
}