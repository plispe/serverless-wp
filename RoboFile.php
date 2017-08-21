<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */


class RoboFile extends \Robo\Tasks
{
    public function wordpressInit()
    {
      $this->taskExecStack()
       ->stopOnFail()
       ->exec('cp -R public/wp/{wp-content,index.php} public/')
       ->exec('cp wp-config-template.php public/wp-config.php')
       ->run();

       $this->taskReplaceInFile('public/index.php')
        ->from("'/wp-blog-header.php'")
        ->to("'/wp/wp-blog-header.php'")
        ->run();
    }

    public function wordpressInitDotEnvFile()
    {
      if (file_exists('.env')) {
        $this->say('File ".env" already exists.');
      } else {
        // Get generated keys and salts from wordpress api
        $content = file_get_contents('https://api.wordpress.org/secret-key/1.1/salt/');
        $envFileContent = '';
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $content) as $line){
          preg_match("~'(.+?)'.+'(.+?)'~", $line, $match);
          if (count($match) === 3) {
            $envFileContent .= sprintf("%s=\"%s\"\n", $match[1], $match[2]);
          }
        }

        file_put_contents('.env', $envFileContent);
        file_put_contents('.env.back', $envFileContent);
      }


    }
}
