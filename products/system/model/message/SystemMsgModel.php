<?php
/**
 *
 */
class SystemMsgModel extends MsgModel
{
	
	
	function SystemMsgModel()
	{
		$message = array(
			'edit_error'           => '<div class="alert alert-danger">入力内容に誤りがあります</div>',
			'update_comp'          => '<div class="alert alert-success">更新完了しました</div>',
			'request_comp'         => '<div class="alert alert-success">申請を行いました</div>',
			'company_request_comp' => '<div class="alert alert-success">企業情報の変更を受け付けました。運営からの承認後、掲載開始されますのでしばらくお待ちください。<br />続いて募集職種、店舗情報を必要に応じて変更してください</div>',
			
			'salon_request_comp'   => '<div class="alert alert-success">変更を受け付けました。運営からの承認後、掲載開始されますのでしばらくお待ちください。</div>',
			'insert_comp'          => '<div class="alert alert-success">登録完了しました</div>',
			'delete_comp'          => '<div class="alert alert-success">削除完了しました</div>',
			'gift_apply'           => '<div class="alert alert-success">申請を承認しました</div>',
			'save_comp'           => '<div class="alert alert-success">一時保存しました</div>',
		);
		$this->setMessages($message);
	}
}