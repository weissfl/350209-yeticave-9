<?php
require_once('helpers.php');

$winner = getWinner() ?? [];

if (count($winner) > 0) {
    $transport = new Swift_SmtpTransport("phpdemo.ru", 25);
    $transport->setUsername(SMTP_USERNAME);
    $transport->setPassword(SMTP_PASSWORD);

    $mailer = new Swift_Mailer($transport);

    $logger = new Swift_Plugins_Loggers_ArrayLogger();
    $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

    $message = new Swift_Message();
    $message->setSubject('Ваша ставка победила!');
    $message->setFrom(["keks@phpdemo.ru" => "Yeti Cave"]);

    foreach ($winner as $item) {
        $set_winner = setWinner([$item["user_id"], $item["lot_id"]]);

        if ($set_winner) {
            $message->addTo($item["email"], $item["username"]);

            $message_body = include_template("email.php", ["data" => $item]);
            $message->setBody($message_body, "text/html");

            $result = $mailer->send($message);

            if ($result) {
                $log = date('Y-m-d H:i:s') . "  Уведомление для лота id: " . $item['lot_id'] . ' успешно отправлено';
            } else {
                $log = date('Y-m-d H:i:s') . " Не удалось отправить уведмление для: " . $logger->dump() . "лота id: " . $item['lot_id'];
            }
            file_put_contents(__DIR__ . '/mail_log.txt', $log . PHP_EOL, FILE_APPEND);
        }
    }
}
