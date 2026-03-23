<?php
#[\AllowDynamicProperties]
/**
 * Comments class.
 */
class WIComment
{
    /**
     * @var 
     */

    /**
     * @var ASUser
     */
    private $users;

    /**
     * Class constructor
     * @param ASDatabase $db
     * @param ASUser $users
     */
    public function __construct()
    {
       $this->WIdb   = WIdb::getInstance();
       $this->users  = new WIUser(WISession::get('user_id'));

    }

    /**
     * Inserts comment into database.
     * @param int $userId Id of user who is posting the comment.
     * @param string $comment Comment text.
     */
    public function insertComment( $userId, $id, $comment)
    {
        if ($error = $this->validateComment($comment)) {
            respond(array(
                'errors' => array('comment' => $error)
            ), 422);
        }

        $userInfo = $this->users->getInfo($userId);
        $datetime = date("Y-m-d H:i:s");

        $this->WIdb->insert("wi_blog_comments", array(
            "blog_id" => $id,
            "posted_by" => $userId,
            "posted_by_name" => $userInfo['username'],
            "comment" => strip_tags($comment),
            "post_time" => $datetime
        ));

        $result = array(
             "user" => $userInfo['username'],
            "comment" => stripslashes(strip_tags($comment)),
            "postTime" => $datetime   
        );
        echo json_encode($result);
    }

    /**
     * @param $comment
     * @return mixed|null|string
     */
    private function validateComment($comment)
    {
        if(trim($comment) == ""){
           return WILang::get('field_required');
        }

    }

    /**
     * Return all comments left by a user.
     * @param int $userId Id of user.
     * @return array Array of all user's comments.
     */
    public function getUserComments($userId)
    {
        return $this->WIdb->select(
            "SELECT * FROM `wi_blog_comments` WHERE `posted_by` = :id",
            array("id" => $userId)
        );
    }

     public function getBlogComments($blogId)
    {
        return $this->WIdb->select(
            "SELECT * FROM `wi_blog_comments` WHERE `blog_id` = :id",
            array("id" => $blogId)
        );
    }

    /**
     * Return last $limit (default 7) comments from database.
     * @param int $limit Required number of comments.
     * @return array Array of comments.
     */
    public function getComments($limit = 7)
    {
        $limit = (int) $limit;

        return $this->WIdb->select("SELECT * FROM `wi_blog_comments` ORDER BY `post_time` DESC LIMIT $limit");
    }
}
