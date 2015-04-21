<?php
/**
 * エスケープヘルパー
 *
 */



/**
 * 企業情報HTMLエスケープ
 *
 */
function escapeHtmlCompany($company)
{
	foreach($company as $key => $val)
	{
		switch($key)
		{
			case 'main_area':
			case 'treatment':
				break;
			default:
				$company[$key] = escapeHtml($val);
		}
	}		
	return $company;
}
	
/**
 * サロン情報HTMLエスケープ
 *
 */
function escapeHtmlSalon($salon)
{
	foreach($salon as $key => $val)
	{
		switch($key)
		{
			case 'station':
			case 'work_time':
			case 'job_salary_ids':
			case 'job_category':
			case 'job_employment':
			case 'holiday_type':
			case 'treatment':
			case 'company_treatment':
			case 'search_treatment':
			case 'photos':
				
				break;
			default:
				$salon[$key] = escapeHtml($val);
		}
	}
	return $salon;
}

/**
 * 募集情報HTMLエスケープ
 *
 */
function escapeHtmlJob_salary($job_salary)
{
	foreach($job_salary as $key => $val)
	{
		switch($key)
		{
			case 'price':
			case 'license_id':
			case 'job_treatment':
				break;
			default:
				$job_salary[$key] = escapeHtml($val);
		}
	}
	return $job_salary;
}

