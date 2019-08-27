<?php
require __DIR__."/vendor/autoload.php";
set_time_limit(0);
$instagram = new \InstagramAPI\Instagram(false, false);
$signature = \InstagramAPI\Signatures::generateUUID();

$liker = array(
    'username' => 'kotea.ru', // Логин аккаунта 
	'password' => 'kotea1234', // Пароль аккаунта
    'interval' => 5, // задержка между лайканием
    'error' => 10, // Время ожидания, если получена какая-либо ошибка
   
);

try
{
   
    $instagram->login($liker['username'], $liker['password']);
    echo(" Успешно авторизовали акк  {$liker['username']}<br>");
    sleep(2);

    echo(" Поулчаем ленту новостей ...<br>");
    $next_max_id = null;
    do
    {
        $feeds = $instagram->timeline->getTimelineFeed($next_max_id);
        foreach($feeds->getFeedItems() as $feed)
        {
            if($feed->isMediaOrAd() == 1)
            {
                if($feed->getMediaOrAd()->isId() && empty($feed->getMediaOrAd()->isHasLiked()))
                {
                    $like = $instagram->media->like($feed->getMediaOrAd()->getId());
                    if($like->getStatus() == "ok")
                    {
                        echo date("d-m-Y H:i:s")." Успешно лайкнули ".$feed->getMediaOrAd()->getUser()->getUsername()."<br>";
                        sleep($liker['interval']);
                    }
                    else
                    {
                        echo date("d-m-Y H:i:s")." Ошибка отдыхаем {$liker['error']} секунд<br>";
                        sleep($liker['error']);
                    }
                }
            }
        }
        $next_max_id = $feeds->getNextMaxId();
    }
    while($next_max_id !== null);
}
catch(Exception $e)
{
    echo($e->getMessage()."<br>");
}