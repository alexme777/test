<?php
/**
 * The followings are the available columns in table 'tbl_comment':
 * @property integer $id
 * @property string $content
 * @property integer $status
 * @property integer $create_time
 * @property string $author
 * @property string $email
 * @property string $url
 * @property integer $post_id
 */
class Comment extends CActiveRecord
{
	const STATUS_PENDING=1;
	const STATUS_APPROVED=2;
	public $level;
	/**
	 * Returns the static model of the specified AR class.
	 * @return static the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'les_comments';
	}

	 public function deleteComments($where = 1)
	{
	
		parent::afterDelete();
		Comment::model()->deleteAll();
	}
	public function findComments($where = 1)
	{
		return $comments = Comment::model()->findAll();
	}
	public function findParentComments($parent_id = 0)
	{
		return $comments = Comment::model()->findAll(
			array(
			"condition" => "parent_id=".$parent_id,  
			)
		);
	}
	public function findPageComments($start,$num, $parent_id = 0)
	{
		return $comments = Comment::model()->findAll(
			array(
			"offset" => $start,  
			"limit" => $num
			)
		);
	}
	 public function insertComment($data)
	{
	  
		parent::afterSave();
            if($this->isNewRecord){  
             $comment = new Comment;
             $comment->id =     $data['id'];
             $comment->name =   $data['name'];
             $comment->ip =  $data['ip'];
             $comment->client = $data['client'];
			 $comment->comment = $data['comment'];
			 $comment->content_id = $data['content_id'];
			 $comment->parent_id = $data['parent_id'];
			 $comment->time = $data['time'];
             $comment->save();
            } else {
 	// иначе неободимо обновить данные 
             UserProfile::model()->updateAll(array( 'id' =>$data['id'], 
                                                'name' => $data['name'],    
                                                'ip'=>$data['ip'],
                                                'client'=>$data['client'],
												'comment'=>$data['comment'],
												'content_id'=>$data['content_id'],
												'parent_id'=>$data['parent_id'],
												'time'=>$data['time']
                    ), 'id=:id', array(':id'=> $data['name']));
            }
		
	}
}