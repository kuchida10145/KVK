<?php
/**
 *
 */
class FrontMsgModel extends MsgModel
{
	
	
	public function __construct()
	{
		$message = array(
			'edit_error'     => '<div class="box05"><p class="clrred">入力内容に誤りがあります</p></div>',
			'delete'         => '<div class="box05"><p class="clrred">削除しました</p></div>',
			'refusal'        => '<div class="box05"><p class="clrred">辞退しました</p></div>',
			'update_comp'     => '<div class="box05"><p class="clrred">更新しました</p></div>',
			'none_hit_user'   => '<div class="box05"><p class="clrred">会員が存在しません</p></div>',
			'login_error'   => '<div class="box05"><p class="clrred">メールアドレス、またはパスワードが違います</p></div>',
		);
		$this->setMessages($message);
	}
}