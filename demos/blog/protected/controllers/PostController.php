<?php

class PostController extends Controller
{
	public $layout='column2';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to access 'index' and 'view' actions.
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated users to access all actions
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 */
	public function actionView()
	{
		$post=$this->loadModel();
		$comment=$this->newComment($post);

		$this->render('view',array(
			'model'=>$post,
			'comment'=>$comment,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Post;
		if(isset($_POST['Post']))
		{
			$model->attributes=$_POST['Post'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadModel();
		if(isset($_POST['Post']))
		{
			$model->attributes=$_POST['Post'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel()->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$comment = new Comment;
		if($_GET['op'] === 'clear-all'){
			$comment->deleteComments();
			header("Location: /demos/blog/");
		}
		
		$time = time();
		
		if(isset($_POST['uname'])){
			$uname = htmlspecialchars(addslashes($_POST['uname']));
			setcookie("unamecom", $uname, $time + 1209600);   // время существования куки две недели
		}elseif(isset($_COOKIE["unamecom"])){
			$uname = htmlspecialchars(addslashes($_COOKIE["unamecom"]));
		}else{
			$uname = "Аноним";
		}
		
		if(!empty($_POST['uname']) && !empty($_POST['message']) && $_POST['op'] == 'add-comment') { 
			//$comment = mysql_real_escape_string(strip_tags($_POST['message'], "<p><b><i><font><img>")); // удалим левые теги
			$message = htmlspecialchars(addslashes($_POST['message']));
			$ip = $_SERVER['REMOTE_ADDR'];
			$client = $_SERVER['HTTP_USER_AGENT'];
			$content_id = intval($_POST['content']);
			$parent_id = intval($_POST['parent']);
			
			$data['id'];
			$data['name'] = $uname;
			$data['ip'] = $ip;
			$data['client'] = $client;
			$data['comment'] = $message;
			$data['content_id'] = $content_id;
			$data['parent_id'] = $parent_id;
			$data['time'] = $time;
			
			$comment->insertComment($data);
		}
		
		
		
		
		
		
		
		
		$page = 0;
		if(!empty($_GET['page'])){
			$page = intval($_GET['page']);
		};
		$num = 5; // Переменная хранит число сообщений выводимых на станице
		if ($page==0) $page=1;
		// Определяем общее число сообщений в базе данных
		// Берем из БД кол-во записей

	
		$msg = array();
		$msg = $comment->findComments();
		$parent_msg = $comment->findParentComments();
		$count = count($parent_msg);
		$posts = $count; // получем значение кол-во всех записей
		// Находим общее число страниц
		$total = intval(($posts - 1) / $num) + 1;

		// Определяем начало сообщений для текущей страницы
		$page = intval($page);
		// Если значение $page меньше единицы или отрицательно
		// переходим на первую страницу
		// А если слишком большое, то переходим на последнюю
		if(empty($page) or $page < 0) $page = 1;
		if($page > $total) $page = $total;
		// Вычисляем начиная к какого номера
		// следует выводить сообщения
		$start = $page * $num - $num;
		
		$msg_page = $comment->findPageComments($start,$num);

		$content_id = 0;  

		// выводим комменты
		
	
		$count = count($msg);
		$count_all = count($msg);
		$parent = 0;
		$form = "<div class='editor'>
		<form id='comment-form' autocomplete='off' method='post'>
		<input type='hidden' name='op' value='add-comment'>
		<input type='hidden' name='content' value='{$id}'>
		<input type='hidden' name='parent' value='{$parent}'>
		<table border='0'><tr><td><input id='uname' name='uname' type='text' value='{$uname}' maxlength='20' size='25' /></td><td>Ваше имя*</td></tr></table>
		<textarea name='message' rows='5' cols='65'></textarea><br><input id='submit' name='signup' type='submit' value='Добавить' /></div>
		</form>";

		$post = "<div class='comments-all'><span style='float:left'>Всего комментариев: {$count_all} </span><span style='margin-left:20px;' class='sort-elements'>Сортировать</span><span class='add-comment'>Написать комментарий</span></div>".$form;
		$post .= "<div class='list-comments'>";
		
		// функция сортирует массив по деревьям
		function crazysort(&$comments, $post = '', $parentComment = 0, $level = 0, $count = null){
			
		  if (is_array($comments) && count($comments)){
			$return = array();
			if (is_null($count)){
			  $c = count($comments);
			}else{
			  $c = $count;
			}
			
			for($i=0;$i<$c;$i++){
			  if (!isset($comments[$i])) continue;
			  $comment = $comments[$i];
			  $parentId = $comment['parent_id'];
			  $date = date("d.m.Y в H:i",$comment['time']);
			  if ($parentId == $parentComment){
				$comment['level'] = $level;
				if($level == 0){$margin=0;}else{$margin=20;};
				$commentId = $comment['id'];
				$post .= "<div class='alone-comment' data-level='{$level}' id='msg{$comment['id']}' style='margin-left: {$margin}px'>";
				$post .= "<div class='comment-title'><span style='float:left'><b>{$comment['name']}</b> <small>({$date})</small></span><span class='comment-ans' id={$comment['id']}>ответить</span></div><div class='comment-message'>{$comment['comment']}</div>";
				$post_child = '';
				$post .= crazysort($comments, $post_child, $commentId, $level+1, $c);
				$post .= '</div>';
			  }
			}
			return $post;
		  }
		  return false;
		}

		$msg = crazysort($msg);
		$post .= $msg;

		$this->render('index',array(
		'comments'=>$post,
		'page'=>$page,
		'total'=>$total,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Post('search');
		if(isset($_GET['Post']))
			$model->attributes=$_GET['Post'];
		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Suggests tags based on the current user input.
	 * This is called via AJAX when the user is entering the tags input.
	 */
	public function actionSuggestTags()
	{
		if(isset($_GET['q']) && ($keyword=trim($_GET['q']))!=='')
		{
			$tags=Tag::model()->suggestTags($keyword);
			if($tags!==array())
				echo implode("\n",$tags);
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
			{
				if(Yii::app()->user->isGuest)
					$condition='status='.Post::STATUS_PUBLISHED.' OR status='.Post::STATUS_ARCHIVED;
				else
					$condition='';
				$this->_model=Post::model()->findByPk($_GET['id'], $condition);
			}
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

	/**
	 * Creates a new comment.
	 * This method attempts to create a new comment based on the user input.
	 * If the comment is successfully created, the browser will be redirected
	 * to show the created comment.
	 * @param Post the post that the new comment belongs to
	 * @return Comment the comment instance
	 */
	protected function newComment($post)
	{
		$comment=new Comment;
		if(isset($_POST['ajax']) && $_POST['ajax']==='comment-form')
		{
			echo CActiveForm::validate($comment);
			Yii::app()->end();
		}
		if(isset($_POST['Comment']))
		{
			$comment->attributes=$_POST['Comment'];
			if($post->addComment($comment))
			{
				if($comment->status==Comment::STATUS_PENDING)
					Yii::app()->user->setFlash('commentSubmitted','Thank you for your comment. Your comment will be posted once it is approved.');
				$this->refresh();
			}
		}
		return $comment;
	}
}
