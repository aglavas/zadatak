<?php

namespace Src;

use Illuminate\Filesystem\Filesystem;
class Helpers
{

    /**
     * Creates folder structure for image upload
     *
     * @param $user_id
     * @return bool
     */

    public static function createFolderStructure($user_id)
    {
        $filesystem = new Filesystem();
        $basePath = str_replace("src", "", dirname(__FILE__));

        if(!$filesystem->isDirectory($basePath.'Storage/'.$user_id))
        {
            $filesystem->makeDirectory($basePath.'Storage/'.$user_id);
        }

        if(!$filesystem->isDirectory($basePath.'Storage/'.$user_id.'/Cover'))
        {
            $filesystem->makeDirectory($basePath . 'Storage/' . $user_id . '/Cover');
        }

        if(!$filesystem->isDirectory($basePath.'Storage/'.$user_id.'/Profile'))
        {
            $filesystem->makeDirectory($basePath . 'Storage/' . $user_id . '/Profile');
        }

    }

    /**
     * Gets base path of project
     *
     * @return mixed
     */

    public static function path()
    {
        return str_replace("src", "", dirname(__FILE__));
    }

}

