<?php
include_once "api.php";
// Step 1: create a new list
$list_id = list_create();
echo "List ".$list_id." created!<br>";

// Step 2: add my email to the list
$my_mail = "tempsteve@mail-apps.com";
if(list_member_create($my_mail, $list_id) === true)
    echo $my_mail." added!<br>";

// Step 3: add another email addresses to the list
$email_list = array();
for ($i=0; $i < 10; $i++) {
    array_push($email_list, md5(mt_rand())."@abc.com");
}
foreach ($email_list as $email) {
    if(list_member_create($email, $list_id) === true)
        echo $email." added!<br>";
}

// Step 4: create a new campaign
$campaign_id = campaign_create($list_id);
echo "Campaign ".$campaign_id." created!<br>";

// Step 5: edit campaign's content
campaign_content_update($campaign_id);

// Step 6: send a campaign email to all the members in the list
if(campaign_send($campaign_id) === true)
    echo "Sent!<br>";
?>
