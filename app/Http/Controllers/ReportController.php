<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dispatch;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\DispatchTicket;
use App\Models\AssignDispatch;
use App\Models\AssignDispatchBrokerVehicle;
use App\Models\Customer;
use App\Models\Vehicle;
use Auth;
use Validator;
use Session;
use DB;
use Carbon\Carbon;
use PDF;
use Config;

use App\Models\CredsDetail;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Facades\Customer as quickbookCustomer; 
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice as QuickbookInvoice;
use QuickBooksOnline\API\Facades\Bill as QuickbookBill;
use QuickBooksOnline\API\Facades\BillPayment as QuickbookBillPayment;
class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 public function invoice_sent(Request $request) {
		 $id = $request->id;
		 $invoice_sent_date = date('Y-m-d H:i:s');
		 $dispatch = Dispatch::find($id);
		 $dispatch->invoice_sent = 'completed';
		 $dispatch->invoice_sent_date = $invoice_sent_date;
		 $dispatch->save();
		 echo 'done';
	 }
	 public function add_user_transaction(Request $request){
		 $data  = $request->all();
		 $user_id = $data['user_id'];
		 $expense = $amount = $data['amount'];
		 $trans_type = $data['trans_type'];
		 $message = $data['message'];
		 $table_name = $data['table_name'];
		 $user_last_amount = $user_total_amount = $user_all_amount = 0;
		 $user_type = 'employee';
		 $user_query = $get_user_trans = '';
		 if($table_name == 'users') {
			 $get_user_trans = Transaction::where('user_id', $user_id)->where('user_type', $user_type)->orderBy('id', 'desc')->take(1)->get()->toArray();
			 $user_query = User::where('id','=',$user_id)->get()->toArray();
			 $user = $user_query[0];
			$user_all_amount = $user['total_income'];
		 } else {
			 $user_type = 'customer';
			 $get_user_trans = Transaction::where('user_id', $user_id)->where('user_type', $user_type)->orderBy('id', 'desc')->take(1)->get()->toArray();
			 $user_query = Customer::where('id','=',$user_id)->get()->toArray();
			 $user = $user_query[0];
			$user_all_amount = $user['total_amount'];
		 }
		 if(isset($get_user_trans) && !empty($get_user_trans)) {
					 $user_last_amount = $get_user_trans[0]['total_amount'];
				 }else {}
		 if($trans_type == 'debit') {
			 $user_total_amount = $user_last_amount - $amount;
		 } else {
			 $user_total_amount = $amount + $user_last_amount;
			 $user_all_amount = $user_all_amount + $amount;
		 }
		 $user_date = date('Ymdhis');
		 $default_transaction_number = 'JPGTN'.$user_date;
		 $user_tran_data = array(
		 'default_transaction_number' => $default_transaction_number,
		 'user_id' => $user_id,
		 'user_type' => $user_type,
		 'trans_genrate_type' => 'extra',
		 'type' => $trans_type,
		 'amount' => $expense,
		 'total_amount' => $user_total_amount,
		 'message' => $message,
		 );
		 $user_trans = Transaction::Create($user_tran_data);
		 /***it is for user update  ***/
				$user_where = array('id' => $user_id);
				$user_update = array('current_amount' => $user_total_amount);
				if($table_name == 'users') {
					$user_update['total_income'] = $user_all_amount;
				} else {
					$user_update['total_amount'] = $user_all_amount;
				}
				DB::table($table_name)
				->where($user_where)
				->update($user_update);
		$data_arr = array('msg' => 'save','current_amount' => $user_total_amount);
		 echo json_encode($data_arr);
		 die;
	 }
	 public function quickbook_download_pdf(Request $request){
		$data  = $request->all();
		$msg = '';
		 $rowid = $data['rowid'];
		 $quickbookid = $data['quickbookid'];
		 /******** call invoice api  ***********/
		$quickbook_creds = $this->get_quickbook_creds();
		$quickbook_creds_arr = json_decode($quickbook_creds,true);
		//Add a new Invoice
		if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && isset($quickbook_creds_arr['api_error'])) {
			$quickbook_invoice_res = $quickbook_creds_arr['api_error'];
		return \Response::json(["status" => 'error','message' => $quickbook_invoice_res, "type" => '']);
		} else {}
		if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr)) {
			$dataService = DataService::Configure($quickbook_creds_arr);
			$dataService->throwExceptionOnError(true);
			try {
			$quickbook_invoice = QuickbookInvoice::create([
				"Id" => $quickbookid
			]);
			$path = public_path('images/pdf');
			$directoryForThePDF = $dataService->DownloadPDF($quickbook_invoice, $path);
			$pdf_name = str_replace($path,'',$directoryForThePDF);
			$invoice_where = array('id' => $rowid);
			$invoice_update = array('invoice_pdf' => $pdf_name);
			DB::table('invoices')
					->where($invoice_where)
					->update($invoice_update);
					return \Response::json(["status" => 'success','message' => $pdf_name, "type" => '']);
			} catch (\Exception $e){
				$message = $e->getMessage();
				return \Response::json(["status" => 'error','message' => $message, "type" => '']);
			}
		}else {
			return \Response::json(["status" => 'error','message' => 'API creds is not Wrong/Inactive', "type" => '']);
		}
	 }
	 public function get_user_current_amount(Request $request){
		 $data  = $request->all();
		 $msg = '';
		 $user_id = $data['user_id'];
		 $amount = $data['amount'];
		 $table_name = $data['table_name'];
		 $user_query = '';
		 if($table_name == 'users') {
			 $user_query = User::where('id','=',$user_id)->get()->toArray();
		 } else {
			 $user_query = Customer::where('id','=',$user_id)->get()->toArray();
		 }
		 
		 $user = $user_query[0];
		 $current_amount = $user['current_amount'];
		 if($current_amount < $amount) {
			 $msg = 'error';
		 }
		 $data_arr = array('msg' => $msg,'current_amount' => $current_amount);
		 echo json_encode($data_arr);
		 die;
	 }
	 public function get_quickbook_invoice_creds() {
		 $res_err = array();
		$quicbook_creds =  CredsDetail::where('creds_for', '=', 'quickbook')->where('use_quickbook_api', '=', 'active')->get()->toArray();
		if(isset($quicbook_creds) && !empty($quicbook_creds)) {
		$quicbook_creds = $quicbook_creds['0'];
		$res_err = $quicbook_creds;
		}
		return json_encode($res_err);
	 }
	 public function get_quickbook_creds() {
		$res_err = array();
		$quicbook_creds =  CredsDetail::where('creds_for', '=', 'quickbook')->where('use_quickbook_api', '=', 'active')->get()->toArray();
		if(isset($quicbook_creds) && !empty($quicbook_creds)) {
		$quicbook_creds = $quicbook_creds['0'];
		$row_id = $quicbook_creds['id'];
			try {
				$oauth2LoginHelper = new OAuth2LoginHelper($quicbook_creds['client_id'],$quicbook_creds['client_secret']);
				/***** Update the OAuth2Token ******/
				$accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($quicbook_creds['refresh_token']);
				$accessTokenValue = $accessTokenObj->getAccessToken();
				$refreshTokenValue = $accessTokenObj->getRefreshToken();
				$creds_details = CredsDetail::find($row_id);
				$creds_details->access_token = $accessTokenValue;
				$creds_details->refresh_token = $refreshTokenValue;
				$creds_details->save();
				/***** end Update the OAuth2Token ******/
				$res_err = array(
				'auth_mode' => $quicbook_creds['auth_mode'],
				'ClientID' => $quicbook_creds['client_id'],
				'ClientSecret' => $quicbook_creds['client_secret'],
				'RedirectURI' => $quicbook_creds['redirect_uri'],
				'accessTokenKey' => $accessTokenValue,
				'refreshTokenKey' => $refreshTokenValue,
				'QBORealmID' => $quicbook_creds['realm_id'],
				'baseUrl' => $quicbook_creds['type']
					);

			} catch (\Exception $e){
				$res_err = array(
					'api_error' => $e->getMessage(),
						);
			}
		} else {}
		return json_encode($res_err);

	}
	 public function generate_employee_invoice(Request $request) {
		 $data  = $request->all();
		 $invoice_id = 100;
		 $for_pdf_name_arr = array();
		 $dispatch_ids = $ticket_ids = array();
		 /*** create dispatch_ids and ticket_ids from ticket_arr ***/
		 foreach($data['ticket_arr'] as $data_ticket_array) {
		    $id = $data_ticket_array['dispatch_id'];
			 $ticketId = $data_ticket_array['ticket_id'];
			 $dispatch_ids[$id] = $id;
			 $ticket_ids[$ticketId] = $ticketId;
		 }
		 /*** end create dispatch_ids and ticket_ids from ticket_arr ***/
		 $invoice_date = date('Y-m-d H:i:s');
		/* $dispatch_ids = $data['dispatch_ids'];
		 $dispatch_ids = array_filter($dispatch_ids); */
		 $ticket_arr = $data['ticket_arr'];
		// $ticket_ids = array_filter($data['ticket_ids']);
		 $dispatch_ids_str = implode(',',$dispatch_ids);
		 $ticket_ids_str = implode(',',$ticket_ids);
		 $quickbook_creds = $this->get_quickbook_creds();
		$quickbook_creds_arr = json_decode($quickbook_creds,true);
		if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && isset($quickbook_creds_arr['api_error'])) {
			$quickbook_invoice_res = $quickbook_creds_arr['api_error'];
		return \Response::json(["status" => 'error','message' => $quickbook_invoice_res, "type" => '']);
		} else {}
		/********get invoice related creds for quickbook************/
		 $quickbook_invoice_hour_id = $quickbook_invoice_load_id = $quickbook_invoice_tax_code_ref = $quickbook_invoice_tax_rate_ref = 0;
		 $quickbook_sale_term_ref = 0;
		  $invoice_creds = $this->get_quickbook_invoice_creds();
		$invoice_creds_arr = json_decode($invoice_creds,true);
		if(isset($invoice_creds_arr) && !empty($invoice_creds_arr)) {
			$quickbook_invoice_hour_id = $invoice_creds_arr['quickbook_emp_bill_hour_id'];
			$quickbook_invoice_load_id = $invoice_creds_arr['quickbook_emp_bill_load_id'];
			$quickbook_invoice_tax_code_ref = $invoice_creds_arr['quickbook_emp_invoice_tax_code_ref'];
			$quickbook_invoice_tax_rate_ref = $invoice_creds_arr['quickbook_emp_invoice_tax_rate_ref'];
			$quickbook_sale_term_ref = $invoice_creds_arr['quickbook_sale_term_ref'];
		} else {}
		foreach($ticket_arr as $ticket_arr_val) {
			$ticket_num = $ticket_arr_val['ticket_number'];
			 $for_pdf_name_arr[] = $ticket_num;
		 }
		 /********end get invoice related creds for quickbook ************/
		 $invoice_data = Invoice::where('user_type','employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
		 if(isset($invoice_data) && !empty($invoice_data)) {
			 $invoice_id = $invoice_data[0]['invoice_number'];
			 $invoice_id = $invoice_id + 1;
		 } else {}
		 $total_load_hour = $total_income = $hst_per_amt = $balance_due = 0;
		 $hst_per = 13;
		$dispatch_data_all =  DispatchTicket::whereIn('id', $ticket_ids)->with('getUser')->with('getDispatch')->with('getDispatch.getCustomer')->get()->toArray();
		$dispatch_data = $dispatch_data_all[0];
		$DueDate = date('Y-m-d');
		$dispatch_data['invoice_id'] = $invoice_id;
		$dispatch_data['invoice_date'] = date('d/m/Y',strtotime($invoice_date));
		//$DueDate = date('Y-m-d', strtotime($invoice_date. ' + 60 days'));
		$dispatch_data['jap_address'] = 'PO Box 30105 Brampton, ON L6R 0S9';
		$dispatch_data['business_note'] = 'Any discrepancy in our invoice must be reported to us within 72 hours after acceptance of the invoice. Failure to inform will be treated that invoice is correct and accepted as is.
		PICK UP LOCATION 488 St. John Sent photo copy before
		Please Make Cheque Payable To JAP GOBIND TRANSPORT LTD.
		Thank you for your business!';
		$pdf_str = implode('-',$for_pdf_name_arr);
		$pdf_name =  $pdf_str.'.pdf';
		
		$quick_book_description_arr = array();
		/*******it is for quickbook  ********/
		$line_num_count = 1;
		$total_records = 0;
		
		/******* end it is for quickbook  ********/
		foreach($dispatch_data_all as $d_val){
			$start_time = date('Y-m-d',strtotime($d_val['get_dispatch']['start_time']));
			$customer_quickbook_id = $d_val['get_dispatch']['get_customer']['quickbook_id'];
			$customer_name = $d_val['get_dispatch']['get_customer']['company_name'];
			$quick_book_total_load_hour = $quick_book_total_income = 0;
						$total_income += $d_val['expense'];
						$total_load_hour += $d_val['hour_or_load'];
						/*******it is for quickbook  ********/
						$imp_info = '';
						$UnitPrice = $d_val['get_dispatch']['job_rate'];
						$total_load_data = $d_val['hour_or_load'];
						$unit_number = $d_val['unit_vehicle_number'];
						$ticket_number = $d_val['ticket_number'];
						
						 $load_or_hour = ($d_val['get_dispatch']['job_type'] == 'load') ? 'Load' : 'Hourly';
						/*****  these values got from category hidden field from add bill category *********/
						$load_or_hour_quick_book_id = ($d_val['get_dispatch']['job_type'] == 'load') ? $quickbook_invoice_load_id : $quickbook_invoice_hour_id;
						/********  This query provide category mentioned in create bill  *****************/
						/**** $expense_account_query = "select * from Account where AccountType = 'Expense' and Name = 'TestHour'";
								$expense_account_search = $dataService->Query($expense_account_query);
								
								foreach($expense_account_search as $vals) {
									echo $vals->Id.' '.$vals->FullyQualifiedName.'<br>';
								}
						$expense_ap_account_id = $expense_account_item_search[0]->Id;
						$expense_ap_account_name = $expense_account_item_search[0]->Name; ***/
						/********  This query provide category mentioned in create bill.  *****************/
						//$ItemRef = array( "name"=> "Bad Debt Expense","value" => 59);
						$ItemRef = array("value" => $load_or_hour_quick_book_id);
					
						$CustomerRef = array( "name"=> $customer_name,"value" => $customer_quickbook_id);
						// Live
						// HST tax rate will be samefor invoice and bill but in bill need to take value from purchase array
						/* $vals->Id will be tax code and $vals->PurchaseTaxRateList->TaxRateDetail->TaxRateRef will be tax rate
						/* $tax_code_query = "SELECT * FROM TaxCode";
						$tax_code_query_search = $dataService->Query($tax_code_query);
						foreach($tax_code_query_search as $vals) {
							if(isset($vals->PurchaseTaxRateList->TaxRateDetail->TaxRateRef)) {
								echo $vals->Id.' || '.$vals->Name.' || '.$vals->Description.' || '.$vals->PurchaseTaxRateList->TaxRateDetail->TaxRateRef.'<br>';
							//	print_r($vals->name);
							}
						} */
						 $SalesItemLineDetail = array('TaxCodeRef' => array("value" => $quickbook_invoice_tax_code_ref),
						// $SalesItemLineDetail = array(
						  "AccountRef" => $ItemRef,
						  "CustomerRef" => $CustomerRef,
						  "BillableStatus" => 'Billable',
						);
						$quick_book_total_income = $d_val['expense'];
						
						$start_location = $d_val['get_dispatch']['start_location'];
						$dump_location = $d_val['get_dispatch']['dump_location'];
						$imp_info = "Unit #$unit_number Ticket #$ticket_number Pick up $start_location Dump at $dump_location\n";
						$imp_info .= "Unit Price #$UnitPrice Qty #$total_load_data\n";
						$quick_book_description_arr[] = array(
						 'Description' => $imp_info,
						 "DetailType" => 'AccountBasedExpenseLineDetail', 
						 "Id" => $line_num_count, 
						  "Amount" => $quick_book_total_income,
						 'AccountBasedExpenseLineDetail' => $SalesItemLineDetail,
						  
						);
						/*******end it is for quickbook  ********/
					
					/*******it is for quickbook  ********/
					$line_num_count = $line_num_count + 1;
					$total_records = $total_records + 1;
					/******* end it is for quickbook  ********/
				}
				$hst_per_amt = ($total_income * $hst_per) / 100;
				$balance_due = $total_income + $hst_per_amt;
				/*******it is for quickbook  ********/
				$quickbook_invoice_id = 0;
				$quickbook_invoice_res = '';
				$TxnDate = $invoice_date;
				$TotalAmt = $balance_due; 
				$TotalTax = $hst_per_amt;
				
				$quickbook_customer_id =  $dispatch_data['get_user']['quickbook_id'];
				$SalesTermRef = array("value" => $quickbook_sale_term_ref); // It will print Net 30
				$LinkedTxn = array("TxnId" => $invoice_id,"TxnType" => "BillPaymentCheck"); // LinkedTxn.TxnId as the ID of the transaction.
				$CustomerMemo = array("value" => "Any discrepancy in our invoice must be reported to us within 72 hours after acceptance of the invoice. Failure to inform will be treated the invoice is correct and accepted as is.  Please make cheque payable to JAP GOBIND TRANSPORT LTD.  Thank you for your business! HST: 824726889RT0001");
				$tax_line =  array("DetailType" => "TaxLineDetail", "Amount" => $TotalTax,'TaxLineDetail' => array(
		"NetAmountTaxable" => $TotalTax, 
		"TaxPercent" => $hst_per,
		"PercentBased" => true,
		"TaxRateRef" => array(
              "value"=> $quickbook_invoice_tax_rate_ref  // Check line number 304
            )));
			/* $query = "SELECT * FROM Item where Name = 'Loads'";
			$item_search = $dataService->Query($query);
			echo '<pre>';
			print_r($item_search[0]->Id);
			echo '</pre>';
			$query = "SELECT * FROM TaxRate";
			$tax_rate_search = $dataService->Query($query);
			die; */
			// Sandbox it is added as HST value 4 got from quickbook by checking hidden field in Tax->add/edit tax rate
		// $TxnTaxDetail = array( 'TxnTaxCodeRef' => array('value' => $quickbook_invoice_tax_code_ref),'TotalTax' => $TotalTax,'TaxLine' => $tax_line);
			// Live
		 $TxnTaxDetail = array('TotalTax' => $TotalTax,'TaxLine' => $tax_line);
		// $TemplateRef = array( 'value' => '5000000000000285338');  // All data and design will reflect based on default selected template
				
				/******** call invoice api  ***********/
				
				if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr)) {
				//	$pdf_name = '';
					$dataService = DataService::Configure($quickbook_creds_arr);
					$dataService->throwExceptionOnError(true);
					//$account_query = "select * from Account where FullyQualifiedName = 'Accounts Payable (A/P)'";
					$account_query = "select * from Account where Classification = 'Liability' && AccountSubType = 'AccountsPayable'";
					// $account_query = "select * from Account where FullyQualifiedName = 'Accounts Payable'";
					
					$account_item_search = $dataService->Query($account_query);
					
					$ap_account_id = $account_item_search[0]->Id;
					$ap_account_name = $account_item_search[0]->Name;
					$APAccountRef = array("name" => 'Accounts Payable', "value" => $ap_account_id);

					$request_arr = array(
					'SyncToken' => '2',
					"domain"=> "QBO", 
					"GlobalTaxCalculation" => "TaxInclusive", 
					"APAccountRef"=> $APAccountRef, 
					"VendorRef"=> [
						  "value"=> $quickbook_customer_id
					],
					'TxnDate' => $TxnDate,
					'TotalAmt' => $TotalAmt,
					"SalesTermRef" => $SalesTermRef,
					"LinkedTxn" => $LinkedTxn,
					"DueDate" => $DueDate,
					"sparse" => false,
					"Line" => $quick_book_description_arr,
					"DocNumber" => $invoice_id, 
					 "TxnTaxDetail" => $TxnTaxDetail,
					// "MetaData" => $CustomerMemo['value'],
					 "PrivateNote" => $CustomerMemo['value'],
					
				);
				/* echo '<pre>';
				print_r($request_arr);
				echo '</pre>';
				die; */
				 $theResourceObj = QuickbookBill::create($request_arr);
				 try {
				$resultingObj = $dataService->Add($theResourceObj);
				$error = $dataService->getLastError();
				if ($error) {
					$quickbook_invoice_res = "The Status code is: " . $error->getHttpStatusCode() . "\n";
					$quickbook_invoice_res .= "The Helper message is: " . $error->getOAuthHelperError() . "\n";
					$quickbook_invoice_res .= "The Response message is: " . $error->getResponseBody() . "\n";
				}
				else {
				$quickbook_invoice_id = $resultingObj->Id;
				}
			} catch (\Exception $e){
				$quickbook_invoice_res = $e->getMessage(); 
				return \Response::json(["status" => 'error','message' => $quickbook_invoice_res, "type" => '']);
			 
			}
		} else {}
		
		/********end call invoice api  ***********/
		/*******end it is for quickbook  ********/
		$invoice = new Invoice;
		$invoice->user_id = $dispatch_data['get_user']['id'];
		$invoice->user_type = 'employee';
		$invoice->customer_id = 0;
		$invoice->invoice_number = $invoice_id;
		$invoice->quickbook_invoice_id = $quickbook_invoice_id;
		$invoice->quickbook_invoice_res = $quickbook_invoice_res;
		$invoice->invoice_date = $invoice_date;
		$invoice->dispatch_ids = $dispatch_ids_str;
		$invoice->ticket_ids = $ticket_ids_str;
		$invoice->subtotal = $total_income;
		$invoice->hst_per = $hst_per;
		$invoice->hst_amount = $hst_per_amt;
		$invoice->total = $balance_due;
		$invoice->invoice_pdf = $pdf_name;
        $invoice->save(); 
		$invoice_generated_id = $invoice->id;
		/*** udate invoice id in Dispatch ticket table  ******/
		 foreach($ticket_arr as $ticket_arr_val) {
			 $ticket_id = $ticket_arr_val['ticket_id'];
			 $where = array('id' => $ticket_id);
			 $data = array('invoice_id' => $invoice_generated_id,'employee_invoice_generate_status' => 'generated');
			 DB::table('dispatch_tickets')
				->where($where)
				->update($data);
		 }
		/*** udate invoice id in Dispatch ticket table  ******/
		
			$all_total_income = $total_income;
			$path = public_path('images/pdf/').$pdf_name;
			$pdf = PDF::loadView('reports.employee_invoice_pdf',compact('dispatch_data','dispatch_data_all','all_total_income','hst_per','hst_per_amt','balance_due'))->save($path );
			$current_pdf_url =   url('/').'/images/pdf/'.$pdf_name;
			return \Response::json(["status" => 'success','message' => $current_pdf_url, "type" => '']);
	  	
	 }
	 public function generate_multi_row_pdf(Request $request) {
		
		 $data  = $request->all();
		 $invoice_id = 100;
		 $for_pdf_name_arr = array();
		 $invoice_date = date('Y-m-d H:i:s');
		 $ticket_arr = $data['ticket_arr'];
		 $dispatch_ids = $data['dispatch_ids'];
		 $dispatch_ids = array_filter($dispatch_ids);
		 $dispatch_ids_str = implode(',',$dispatch_ids);
		 
		 $quickbook_creds = $this->get_quickbook_creds();
		$quickbook_creds_arr = json_decode($quickbook_creds,true);
		if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && isset($quickbook_creds_arr['api_error'])) {
			$quickbook_invoice_res = $quickbook_creds_arr['api_error'];
		return \Response::json(["status" => 'error','message' => $quickbook_invoice_res, "type" => '']);
		} else {}
		 /********get invoice related creds for quickbook************/
		 $quickbook_invoice_hour_id = $quickbook_invoice_load_id = $quickbook_invoice_tax_code_ref = $quickbook_invoice_tax_rate_ref = 0;
		 $quickbook_sale_term_ref = 0;
		  $invoice_creds = $this->get_quickbook_invoice_creds();
		$invoice_creds_arr = json_decode($invoice_creds,true);
		if(isset($invoice_creds_arr) && !empty($invoice_creds_arr)) {
			$quickbook_invoice_hour_id = $invoice_creds_arr['quickbook_invoice_hour_id'];
			$quickbook_invoice_load_id = $invoice_creds_arr['quickbook_invoice_load_id'];
			$quickbook_invoice_tax_code_ref = $invoice_creds_arr['quickbook_invoice_tax_code_ref'];
			$quickbook_invoice_tax_rate_ref = $invoice_creds_arr['quickbook_invoice_tax_rate_ref'];
			$quickbook_sale_term_ref = $invoice_creds_arr['quickbook_sale_term_ref'];
		} else {}
		 /********end get invoice related creds for quickbook ************/
		 foreach($ticket_arr as $ticket_arr_val) {
			$ticket_num = $ticket_arr_val['ticket_number'];
			$dispatch_id = $ticket_arr_val['dispatch_id'];
			 $for_pdf_name_arr[] = $ticket_num;
		 }
		 $invoice_data = Invoice::orderBy('id', 'desc')->take(1)->get()->toArray();
		 if(isset($invoice_data) && !empty($invoice_data)) {
			 $invoice_id = $invoice_data[0]['invoice_number'];
			 $invoice_id = $invoice_id + 1;
		 } else {}
		 $total_load_hour = $total_income = $hst_per_amt = $balance_due = 0;
		 $hst_per = 13;
		$dispatch_data_all =  Dispatch::whereIn('id', $dispatch_ids)->with('getCustomer')->with('getDispatchTicket')->get()->toArray();
		$dispatch_data = $dispatch_data_all[0];
		$dispatch_data['invoice_id'] = $invoice_id;
		$dispatch_data['invoice_date'] = date('d/m/Y',strtotime($invoice_date));
		$DueDate = date('Y-m-d', strtotime($invoice_date. ' + 60 days'));
		$dispatch_data['jap_address'] = 'PO Box 30105 Brampton, ON L6R 0S9';
		$dispatch_data['business_note'] = 'Any discrepancy in our invoice must be reported to us within 72 hours after acceptance of the invoice. Failure to inform will be treated that invoice is correct and accepted as is.
		PICK UP LOCATION 488 St. John Sent photo copy before
		Please Make Cheque Payable To JAP GOBIND TRANSPORT LTD.
		Thank you for your business!';
		$pdf_str = implode('-',$for_pdf_name_arr);
		$pdf_name =  $pdf_str.'.pdf';
		$quick_book_description_arr = array();
		/*******it is for quickbook  ********/
		$line_num_count = 1;
		$total_records = 0;
		/******* end it is for quickbook  ********/
		foreach($dispatch_data_all as $dispatch_data_val){
			$start_time = date('Y-m-d',strtotime($dispatch_data_val['start_time']));
			$quick_book_total_load_hour = $quick_book_total_income = 0;
			foreach($dispatch_data_val['get_dispatch_ticket'] as $d_val) {
						$total_income += $d_val['income'];
						$total_load_hour += $d_val['hour_or_load'];
						/*******it is for quickbook  ********/
						$imp_info = '';
						$UnitPrice = $dispatch_data_val['job_rate'];
						$total_load_data = $d_val['hour_or_load'];
						$unit_number = $d_val['unit_vehicle_number'];
						$ticket_number = $d_val['ticket_number'];
						
						 $load_or_hour = ($dispatch_data_val['job_type'] == 'load') ? 'Load' : 'Hourly';
						/*****  thse values got from $query = "SELECT * FROM Item where Name = 'Loads'";  given below *********/
						$load_or_hour_quick_book_id = ($dispatch_data_val['job_type'] == 'load') ? $quickbook_invoice_load_id : $quickbook_invoice_hour_id;
						$ItemRef = array( "name"=> 'Services',"value" => $load_or_hour_quick_book_id);
						// Live $vals->Id is tax code Id and TaxRateRef is rate id
						/* $tax_code_query = "SELECT * FROM TaxCode";
						$tax_code_query_search = $dataService->Query($tax_code_query);
						foreach($tax_code_query_search as $vals) {
							if(isset($vals->SalesTaxRateList->TaxRateDetail->TaxRateRef)) {
								echo $vals->Id.' || '.$vals->Name.' || '.$vals->Description.' || '.$vals->SalesTaxRateList->TaxRateDetail->TaxRateRef.'<br>';
							//	print_r($vals);
							}
						} */
						$SalesItemLineDetail = array('TaxCodeRef' => array("value" => $quickbook_invoice_tax_code_ref),
						//$SalesItemLineDetail = array(
						"Qty" => $total_load_data,
						  "UnitPrice"  => $UnitPrice,
						  "ItemRef" => $ItemRef,
						  "ServiceDate" => $start_time,
						);
						$quick_book_total_income = $d_val['income'];
						
						$start_location = $dispatch_data_val['start_location'];
						$dump_location = $dispatch_data_val['dump_location'];
						$imp_info = "Unit #$unit_number Ticket #$ticket_number Pick up $start_location Dump at $dump_location\n";
						$quick_book_description_arr[] = array(
						 'Description' => $imp_info,
						 "DetailType" => 'SalesItemLineDetail', 
						 'SalesItemLineDetail' => $SalesItemLineDetail,
						  "LineNum" => $line_num_count, 
						  "Amount" => $quick_book_total_income,
						  
						//  "Id" => 6  // not got use of this
						);
						/*******end it is for quickbook  ********/
					}
					/*******it is for quickbook  ********/
					$line_num_count = $line_num_count + 1;
					$total_records = $total_records + 1;
					/******* end it is for quickbook  ********/
		}
		
		$hst_per_amt = ($total_income * $hst_per) / 100;
		$balance_due = $total_income + $hst_per_amt;
		
		
		/*******it is for quickbook  ********/
		$quickbook_invoice_id = 0;
		$quickbook_invoice_res = '';
		$TxnDate = $invoice_date;
		$TotalAmt = $balance_due; 
		$TotalTax = $hst_per_amt;
		$quickbook_customer_id =  $dispatch_data['get_customer']['quickbook_id'];
		$SalesTermRef = array("value" => $quickbook_sale_term_ref); // It will print Net 30
		$CustomerMemo = array("value" => "Any discrepancy in our invoice must be reported to us within 72 hours after acceptance of the invoice. Failure to inform will be treated the invoice is correct and accepted as is.  Please make cheque payable to JAP GOBIND TRANSPORT LTD.  Thank you for your business! HST: 824726889RT0001");
		$quick_book_description_arr[] = array(
		"DetailType"=> "SubTotalLineDetail", 
        "Amount" => $total_income, 
        "SubTotalLineDetail" => ''
		);
		$tax_line =  array("DetailType" => "TaxLineDetail", "Amount" => $TotalTax,'TaxLineDetail' => array(
		"NetAmountTaxable" => $TotalTax, 
		"TaxPercent" => $hst_per,
		"PercentBased" => true,
		"TaxRateRef" => array(
              "value"=> $quickbook_invoice_tax_rate_ref // check on line no 549
            )));
			/* $query = "SELECT * FROM Item where Name = 'Loads'";
			$item_search = $dataService->Query($query);
			echo '<pre>';
			print_r($item_search[0]->Id);
			echo '</pre>';
			$query = "SELECT * FROM TaxRate";
			$tax_rate_search = $dataService->Query($query);
			die; */
			// Sandbox it is added as HST value 4 got from quickbook by checking hidden field in Tax->add/edit tax rate
		// $TxnTaxDetail = array( 'TxnTaxCodeRef' => array('value' => '5'),'TotalTax' => $TotalTax,'TaxLine' => $tax_line);
			// Live
		 $TxnTaxDetail = array('TotalTax' => $TotalTax,'TaxLine' => $tax_line);
		// $TemplateRef = array( 'value' => '5000000000000285338');  // All data and design will reflect based on default selected template
		
		/******** call invoice api  ***********/
		
		if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr)) {
			$pdf_name = '';
			$dataService = DataService::Configure($quickbook_creds_arr);
			$dataService->throwExceptionOnError(true);
			
			$request_arr = array(
		    'TxnDate' => $TxnDate,
			"domain"=> "QBO", 
			"PrintStatus" => "NeedToPrint", 
		    'TotalAmt' => $TotalAmt,
			"Line" => $quick_book_description_arr,
			"CustomerRef"=> [
				  "value"=> $quickbook_customer_id
			],
			  "TxnTaxDetail" => $TxnTaxDetail,
			"SalesTermRef" => $SalesTermRef,
			"CustomerMemo" => $CustomerMemo,
			// "TemplateRef" => $TemplateRef,
			"DueDate" => $DueDate,
		);
		 $theResourceObj = QuickbookInvoice::create($request_arr);
		 
		 try {
		$resultingObj = $dataService->Add($theResourceObj);
		$error = $dataService->getLastError();
		if ($error) {
			$quickbook_invoice_res = "The Status code is: " . $error->getHttpStatusCode() . "\n";
			$quickbook_invoice_res .= "The Helper message is: " . $error->getOAuthHelperError() . "\n";
			$quickbook_invoice_res .= "The Response message is: " . $error->getResponseBody() . "\n";
		}
		else {
		$quickbook_invoice_id = $resultingObj->Id;
		}
	} catch (\Exception $e){
		$quickbook_invoice_res = $e->getMessage();
		return \Response::json(["status" => 'error','message' => $quickbook_invoice_res, "type" => '']);
	 
	}
	 }		
		/********end call invoice api  ***********/
		/*******end it is for quickbook  ********/
		
		$invoice = new Invoice;
		$invoice->customer_id = $dispatch_data['get_customer']['id'];
		$invoice->invoice_number = $invoice_id;
		$invoice->quickbook_invoice_id = $quickbook_invoice_id;
		$invoice->quickbook_invoice_res = $quickbook_invoice_res;
		$invoice->invoice_date = $invoice_date;
		$invoice->dispatch_ids = $dispatch_ids_str;
		$invoice->subtotal = $total_income;
		$invoice->hst_per = $hst_per;
		$invoice->hst_amount = $hst_per_amt;
		$invoice->total = $balance_due;
		$invoice->invoice_pdf = $pdf_name;
        $invoice->save(); 
		$invoice_generated_id = $invoice->id;
		/*** udate ticket number as default_dispatch_number in Dispatch table  ******/
		 foreach($ticket_arr as $ticket_arr_val) {
			 $ticket_num = $ticket_arr_val['ticket_number'];
			 $where = array('id' => $ticket_arr_val['dispatch_id']);
			 $data = array('default_dispatch_number' => $ticket_num,'invoice_id' => $invoice_generated_id,'invoice_date' => $invoice_date,'invoice_sent' => 'completed','invoice_sent_date' => $invoice_date);
			 DB::table('dispatches')
				->where($where)
				->update($data);
		 }
		 /*** end udate ticket number as default_dispatch_number in Dispatch table  ******/
		
	   if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr)) {
			return \Response::json(["status" => 'success','message' => '', "type" => 'quickbook']);
	   } else {
			$all_total_income = $total_income;
			$path = public_path('images/pdf/').$pdf_name;
			$pdf = PDF::loadView('reports.invoice_pdf',compact('dispatch_data','dispatch_data_all','all_total_income','hst_per','hst_per_amt','balance_due'))->save($path );
		// return $pdf->download($pdf_name);
			$current_pdf_url =   url('/').'/images/pdf/'.$pdf_name;
			return \Response::json(["status" => 'success','message' => $current_pdf_url, "type" => '']);
	   }
        
		 
	 }
	 public function generate_pdf(Request $request,$id) {
		 $invoice_id = $invoice_date = '';
		 $dispatch_data =  Dispatch::where('id','=',$id)->with('getCustomer')->with('getDispatchTicket')->get()->toArray();
		 $dispatch_data = $dispatch_data[0];
		 if($dispatch_data['invoice_id'] == '') {
			 $invoice_id = 'JPGI'.date('Ymdhis');
			 $invoice_date = date('Y-m-d H:i:s');
			 $dispatch = Dispatch::find($id);
			 $dispatch->invoice_id = $invoice_id;
			 $dispatch->invoice_date = $invoice_date;
			 $dispatch->save();
		 } else {
			 $invoice_id = $dispatch_data['invoice_id'];
			 $invoice_date = $dispatch_data['invoice_date'];
		 }
		 $dispatch_data['invoice_id'] = $invoice_id;
		 $dispatch_data['invoice_date'] = date('d/m/Y',strtotime($invoice_date));
		 $dispatch_data['jap_address'] = 'PO Box 30105 Brampton, ON L6R 0S9';
		 $dispatch_data['business_note'] = 'Any discrepancy in our invoice must be reported to us within 72 hours after acceptance of the invoice. Failure to inform will be treated that invoice is correct and accepted as is.
		PICK UP LOCATION 488 St. John Sent photo copy before
		Please Make Cheque Payable To JAP GOBIND TRANSPORT LTD.
		Thank you for your business!';
		 $pdf_name =  $dispatch_data['get_customer']['company_name'].'('.$dispatch_data['start_time'].').pdf';
		 $path = public_path('images/pdf/').$pdf_name;
        $pdf = PDF::loadView('reports.invoice_pdf',compact('dispatch_data'))->save($path );
        return $pdf->download($pdf_name);
	 }
	 public function all_invoices(Request $request) {
		if(Auth::user()->can('viewMenu:Invoice') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			DB::enableQueryLog();
			$perPage = 15;
			$dispatch_data = '';
			$start_time = $till_date = '';
			$customer = Customer::get()->pluck('company_name','id');
			$dispatch_date = $request->get('dispatch_date');
			$status = $request->get('status');
			if($dispatch_date != '') {
				$date_arr = explode("-",$dispatch_date);
				$start_time = $date_arr[0];
				$till_date = $date_arr[1];
			} else{}
			$customer_id = $request->get('customer_id');
			$query =  Invoice::query()->where('user_type','customer')->with('getCustomer')->with('dispatches');
			if (!empty($customer_id) || $start_time != '' || $status != '') {
				if($start_time != '') {
					$date_greter_then = date("Y-m-d", strtotime($start_time)); 
					$date_less_then = date("Y-m-d", strtotime($till_date));
					$query->whereDate('created_at','>=',$date_greter_then)
						->whereDate('created_at','<=',$date_less_then);
				}
				if(!empty($customer_id)) {
				$query->whereHas('getCustomer',function ($query)use($customer_id)
				  {
					  $query->where('customers.id','=',$customer_id);
				  });
				}
				if($status != '') {
					$query->where('status','=',$status);
				}
			$dispatch_data = $query->latest()->paginate($perPage);
			} else {
				$dispatch_data = $query->latest()->paginate($perPage);
			}
            return view('reports.all_invoices',compact('dispatch_data','customer'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
	 }
	 public function update_status(Request $request) {
		$data  = $request->all();
		$id = $data['id'];
		$invoice_data = Invoice::find($id);
		$invoice_number = $invoice_data->invoice_number;
		$dispatch_ids = $invoice_data->dispatch_ids;
		$user_id = $invoice_data->customer_id;
		$subtotal = $invoice_data->subtotal;
		$dispatch_id_arr = explode(",",$dispatch_ids);
		foreach($dispatch_id_arr as $id_data) {
		$where = array('dispatch_id' => $id_data,'status' => 'completed');
		$update_completed = array('paid_to_company' => 'completed');
		 DB::table('dispatch_tickets')
			->where($where)
			->update($update_completed);
		}
		$date = Carbon::now();
		$invoice_data->status = 'completed';
		$invoice_data->completed_date = $date;
		$invoice_data->save();
		/******** debit customer amount */
		$user_last_amount = $user_total_amount = $user_all_amount = 0;
		$user_query = $get_user_trans = '';
		$user_type = 'customer';
		$trans_type = 'debit';
		$expense = $subtotal;
		$table_name = 'customers';
		$get_user_trans = Transaction::where('user_id', $user_id)->where('user_type', $user_type)->orderBy('id', 'desc')->take(1)->get()->toArray();
		$user_query = Customer::where('id','=',$user_id)->get()->toArray();
		$user = $user_query[0];
		$user_all_amount = $user['total_amount'];
		if(isset($get_user_trans) && !empty($get_user_trans)) {
							$user_last_amount = $get_user_trans[0]['total_amount'];
						}else {}
		$user_total_amount = $user_last_amount - $subtotal;
		$user_date = date('Ymdhis');
		 $default_transaction_number = 'JPGTN'.$user_date;
		 $user_tran_data = array(
		 'default_transaction_number' => $default_transaction_number,
		 'user_id' => $user_id,
		 'user_type' => $user_type,
		 'trans_genrate_type' => 'system',
		 'type' => $trans_type,
		 'invoice_id' => $id,
		 'amount' => $expense,
		 'total_amount' => $user_total_amount,
		 'message' => 'Invoice number :- '.$invoice_number.' paid',
		 );
		 $user_trans = Transaction::Create($user_tran_data);
	$user_where = array('id' => $user_id);
	$user_update = array('current_amount' => $user_total_amount);
	$user_update['total_amount'] = $user_all_amount;
	DB::table($table_name)
				->where($user_where)
				->update($user_update);
		/******** end debit customer amount */
	 }
	 public function invoice(Request $request)
    {
		if(Auth::user()->can('viewMenu:ActionInvoice') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			DB::enableQueryLog();
			$perPage = 15;
			$dispatch_data = '';
			$start_time = $till_date = '';
			$customer = Customer::get()->pluck('company_name','id');
			$dispatch_date = $request->get('dispatch_date');
			if($dispatch_date != '') {
				$date_arr = explode("-",$dispatch_date);
				$start_time = $date_arr[0];
				$till_date = $date_arr[1];
			} else{}
			$customer_id = $request->get('customer_id');
			$query =  Dispatch::query()->with('getCustomer')->with('getDispatchTicket');
			$query->where('status','=','completed');
			if (!empty($customer_id) || $start_time != '') {
				$date_greter_then = date("Y-m-d", strtotime($start_time)); 
					$date_less_then = date("Y-m-d", strtotime($till_date));
				$query->whereDate('start_time','>=',$date_greter_then)
							  ->whereDate('start_time','<=',$date_less_then);
				$query->whereHas('getCustomer',function ($query)use($customer_id)
				  {
					  $query->where('customers.id','=',$customer_id);
				  });
			$dispatch_data = $query->latest()->get();
			} else {
				$dispatch_data = $query->where('id','=','-1')->latest()->get();
			}
            return view('reports.invoice',compact('dispatch_data','customer'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
    public function customer_revenue(Request $request)
    {
		if(Auth::user()->can('viewMenu:Report') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			DB::enableQueryLog();
			$perPage = 15;
			$start_time = $till_date = '';
			$keyword = $request->get('search');
			$dispatch_date = $request->get('dispatch_date');
			$status = $request->get('status');
			$customer = $request->get('customer');
			$ticket = $request->get('ticket');
			$start_location = $request->get('start_location');
			$end_location = $request->get('end_location');
			$status = 'completed';
			if($dispatch_date != '') {
				$date_arr = explode("-",$dispatch_date);
				$start_time = $date_arr[0];
				$till_date = $date_arr[1];
			} else{}
			$query =  DispatchTicket::query()->with('getDispatch')->with('getUser')->with('getBrokerVehicle');
			$query->where('status','=',$status);
			if (!empty($keyword) || $start_time != '' || $customer != '' || $ticket != '' || $start_location != '' || $end_location != '') {
				if($ticket != ''){
					$query->where('ticket_number','Like','%'.$ticket.'%');
				}
				if($start_location != ''){
                    $query->WhereHas('getDispatch',function ($query)use($start_location)
					{
						$query->where('start_location','Like','%'.$start_location.'%');
					});
                }
				if($end_location != ''){
                    $query->WhereHas('getDispatch',function ($query)use($end_location)
					{
						$query->where('dump_location','Like','%'.$end_location.'%');
					});
                }
				if($customer != ''){
					$query->WhereHas('getDispatch',function ($query)use($customer)
					{
						$query->where('dispatches.customer_company_name','Like','%'.$customer.'%')
						->orWhere('dispatches.customer_address','Like','%'.$customer.'%');
					});
				}
				if($start_time != '') {
					$date_greter_then = date("Y-m-d", strtotime($start_time)); 
					$date_less_then = date("Y-m-d", strtotime($till_date));
					$query->WhereHas('getDispatch',function ($query)use($date_greter_then,$date_less_then)
					{
						$query->whereDate('start_time','>=',$date_greter_then)
						->whereDate('start_time','<=',$date_less_then);
					});
				}
				if(!empty($keyword)) {
					// 	$query->where('driver_name', 'LIKE', "%$keyword%")
					// 	->orwhere('ticket_number', 'LIKE', "%$keyword%")
					// ->orwhere('unit_vehicle_number', 'LIKE', "%$keyword%")
					// ->orWhereHas('getDispatch',function ($query)use($keyword)
					// {
					// 	$query->where('dispatches.customer_company_name','Like','%'.$keyword.'%');
					// })->orWhereHas('getBrokerVehicle',function ($query)use($keyword)
					// {
					// 	$query->where('assign_dispatch_broker_vehicles.driver_name','Like','%'.$keyword.'%')
					// 	->orWhere('assign_dispatch_broker_vehicles.vehicle_number','Like','%'.$keyword.'%')
					// 	->orWhere('assign_dispatch_broker_vehicles.contact_number','Like','%'.$keyword.'%');
					// });
				}
			$dispatch_tickets = $query->latest()->get(); 
			} else {
				$dispatch_tickets = DispatchTicket::where('status','=',$status)->latest()->get();
			}
            return view('reports.customer_revenue',compact('dispatch_tickets'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
	public function dispatch_report(Request $request)
    {
		if(Auth::user()->can('viewMenu:Report') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			DB::enableQueryLog();
			$perPage = 15;
			$start_time = $till_date = '';
			$keyword = $request->get('search');
			$dispatch_date = $request->get('dispatch_date');
			$status = $request->get('status');
			$customer = $request->get('customer');
			$start_location = $request->get('start_location');
			$end_location = $request->get('end_location');
			$status = 'completed';
			if($dispatch_date != '') {
				$date_arr = explode("-",$dispatch_date);
				$start_time = $date_arr[0];
				$till_date = $date_arr[1];
			} else{}
			$query =  Dispatch::query()->with('getDispatchCompleteTicket')->with('getCustomer');
			$query->where('status','=',$status);
			if (!empty($keyword) || $start_time != '' || $customer != '' || $start_location != '' || $end_location != '') {
				if($start_location != ''){
                    $query->where('start_location','Like','%'.$start_location.'%');
                }
				if($end_location != ''){
					$query->where('dump_location','Like','%'.$end_location.'%');
                }
				 if($start_time != '') {
					$date_greter_then = date("Y-m-d", strtotime($start_time)); 
					$date_less_then = date("Y-m-d", strtotime($till_date));
					$query->whereDate('start_time','>=',$date_greter_then)
							  ->whereDate('start_time','<=',$date_less_then);;
				}
				if($customer != ''){
					$query->where('customer_company_name','Like','%'.$customer.'%')
						->orWhere('customer_address','Like','%'.$customer.'%');
				}
			$dispatch_tickets = $query->latest()->get(); 
			} else {
			//	$dispatch_tickets = Dispatch::where('status','=',$status)->latest()->paginate($perPage);
				$dispatch_tickets = Dispatch::where('status','=',$status)->latest()->get();
			}
            return view('reports.dispatch_report',compact('dispatch_tickets'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
	public function employee_payment_earning(Request $request)
    {
		if(Auth::user()->can('viewMenu:Report') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			DB::enableQueryLog();
			$perPage = 15;
			$start_time = $till_date = '';
			$keyword = $request->get('search');
			$dispatch_date = $request->get('dispatch_date');
			$status = $request->get('status');
			$customer = $request->get('customer');
			$ticket = $request->get('ticket');
			$driver = $request->get('driver');
			$status = 'completed';
			if($dispatch_date != '') {
				$date_arr = explode("-",$dispatch_date);
				$start_time = $date_arr[0];
				$till_date = $date_arr[1];
			} else{}
			$query =  DispatchTicket::query()->with('getDispatch')->with('getUser')->with('getBrokerVehicle');
			$query->where('status','=',$status);
			if (!empty($keyword) || $start_time != '' || $customer != '' || $ticket != '' || $driver != '') {
				if($ticket != ''){
					$query->where('ticket_number','Like','%'.$ticket.'%');
				}
				if($driver != ''){
					$query->where('driver_name','Like','%'.$driver.'%');
				}
				if($customer != ''){
					$query->WhereHas('getDispatch',function ($query)use($customer)
					{
						$query->where('dispatches.customer_company_name','Like','%'.$customer.'%')
						->orWhere('dispatches.customer_address','Like','%'.$customer.'%');
					});
				}
				if($start_time != '') {
					$date_greter_then = date("Y-m-d", strtotime($start_time)); 
					$date_less_then = date("Y-m-d", strtotime($till_date));
					$query->WhereHas('getDispatch',function ($query)use($date_greter_then,$date_less_then)
					{
						$query->whereDate('start_time','>=',$date_greter_then)
						->whereDate('start_time','<=',$date_less_then);
					});
				}
				if(!empty($keyword)) {
					// 	$query->where('driver_name', 'LIKE', "%$keyword%")
					// 	->orwhere('ticket_number', 'LIKE', "%$keyword%")
					// ->orwhere('unit_vehicle_number', 'LIKE', "%$keyword%")
					// ->orWhereHas('getDispatch',function ($query)use($keyword)
					// {
					// 	$query->where('dispatches.customer_company_name','Like','%'.$keyword.'%');
					// })->orWhereHas('getBrokerVehicle',function ($query)use($keyword)
					// {
					// 	$query->where('assign_dispatch_broker_vehicles.driver_name','Like','%'.$keyword.'%')
					// 	->orWhere('assign_dispatch_broker_vehicles.vehicle_number','Like','%'.$keyword.'%')
					// 	->orWhere('assign_dispatch_broker_vehicles.contact_number','Like','%'.$keyword.'%');
					// });
				}
			$dispatch_tickets = $query->latest()->get(); 
			} else {
				$dispatch_tickets = DispatchTicket::where('status','=',$status)->latest()->get();
			}
            return view('reports.employee_payment_earning',compact('dispatch_tickets'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
	public function employee_bill(Request $request)
    {
		if(Auth::user()->can('viewMenu:ActionInvoice') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			DB::enableQueryLog();
			$perPage = 15;
			$dispatch_data = '';
			$start_time = $till_date = '';
			$employees = User::where('type','employee')->get()->pluck('name','id');
			$dispatch_date = $request->get('dispatch_date');
			if($dispatch_date != '') {
				$date_arr = explode("-",$dispatch_date);
				$start_time = $date_arr[0];
				$till_date = $date_arr[1];
			} else{}
			 $employee_id = $request->get('employee_id');
			$query =  DispatchTicket::query()->with('getUser')->with('getDispatch');
			$query->where('status','=','completed')->where('employee_invoice_generate_status','=','pending');
			if (!empty($employee_id) || $start_time != '') {
				$date_greter_then = date("Y-m-d", strtotime($start_time)); 
					$date_less_then = date("Y-m-d", strtotime($till_date));
				$query->whereHas('getDispatch',function ($query)use($date_greter_then,$date_less_then)
				  {
					  $query->whereDate('dispatches.start_time','>=',$date_greter_then)
							  ->whereDate('dispatches.start_time','<=',$date_less_then);
				  });
				$query->whereHas('getUser',function ($query)use($employee_id)
				  {
					  $query->where('users.id','=',$employee_id);
				  });
			$dispatch_data = $query->latest()->get();
			} else {
				$dispatch_data = $query->where('id','=','-1')->latest()->get();
			}
		$query = DB::getQueryLog();
		
//dd($query);
            return view('reports.employee_bill',compact('dispatch_data','employees'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
	 public function employee_all_bills(Request $request) {
		if(Auth::user()->can('viewMenu:Invoice') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			DB::enableQueryLog();
			$perPage = 15;
			$dispatch_data = '';
			$start_time = $till_date = '';
			$employees = User::where('type','employee')->get()->pluck('name','id');
			$dispatch_date = $request->get('dispatch_date');
			$status = $request->get('status');
			if($dispatch_date != '') {
				$date_arr = explode("-",$dispatch_date);
				$start_time = $date_arr[0];
				$till_date = $date_arr[1];
			} else{}
			$user_id = $request->get('user_id');
			$query =  Invoice::query()->where('user_type','employee')->with('getUser')->with('dispatches');
			if (!empty($user_id) || $start_time != '' || $status != '') {
				if($start_time != '') {
					$date_greter_then = date("Y-m-d", strtotime($start_time)); 
					$date_less_then = date("Y-m-d", strtotime($till_date));
					$query->whereDate('created_at','>=',$date_greter_then)
						->whereDate('created_at','<=',$date_less_then);
				}
				if(!empty($user_id)) {
				$query->whereHas('getUser',function ($query)use($user_id)
				  {
					  $query->where('users.id','=',$user_id);
				  });
				}
				if($status != '') {
					$query->where('status','=',$status);
				}
			$dispatch_data = $query->latest()->paginate($perPage);
			} else {
				$dispatch_data = $query->latest()->paginate($perPage);
			}
            return view('reports.employee_all_bills',compact('dispatch_data','employees'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
	 }
	 public function quickbook_employee_bill_download_pdf(Request $request){
		 return \Response::json(["status" => 'error','message' => "API not implemented yet", "type" => '']);
		$data  = $request->all();
		$msg = '';
		 $rowid = $data['rowid'];
		 $quickbookid = $data['quickbookid'];
		 /******** call invoice api  ***********/
		$quickbook_creds = $this->get_quickbook_creds();
		$quickbook_creds_arr = json_decode($quickbook_creds,true);
		//Add a new Invoice
		if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && isset($quickbook_creds_arr['api_error'])) {
			$quickbook_invoice_res = $quickbook_creds_arr['api_error'];
		return \Response::json(["status" => 'error','message' => $quickbook_invoice_res, "type" => '']);
		} else {}
		if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr)) {
			$dataService = DataService::Configure($quickbook_creds_arr);
			$dataService->throwExceptionOnError(true);
			try {
			$quickbook_invoice = QuickbookInvoice::create([
				"Id" => $quickbookid
			]);
			$path = public_path('images/pdf');
			$directoryForThePDF = $dataService->DownloadPDF($quickbook_invoice, $path);
			$pdf_name = str_replace($path,'',$directoryForThePDF);
			$invoice_where = array('id' => $rowid);
			$invoice_update = array('invoice_pdf' => $pdf_name);
			DB::table('invoices')
					->where($invoice_where)
					->update($invoice_update);
					return \Response::json(["status" => 'success','message' => $pdf_name, "type" => '']);
			} catch (\Exception $e){
				$message = $e->getMessage();
				return \Response::json(["status" => 'error','message' => $message, "type" => '']);
			}
		}else {
			return \Response::json(["status" => 'error','message' => 'API creds is not Wrong/Inactive', "type" => '']);
		}
	 }
	  public function update_employee_payroll_bill_status(Request $request) {
		  $payable_quickbook_invoice_id = 0;
		  $payable_quickbook_invoice_res = '';
		$data  = $request->all();
		$id = $data['id'];
		$invoice_data = Invoice::find($id);
		$invoice_number = $invoice_data->invoice_number;
		$ticket_ids = $invoice_data->ticket_ids;
		$user_id = $invoice_data->user_id;
		$subtotal = $invoice_data->subtotal;
		$total_amount = $invoice_data->total;
		$bill_quickbook_invoice_id = $invoice_data->quickbook_invoice_id;
		$user_vendor_data = $invoice_data->getUser;
		$vendor_quickbook_id = $user_vendor_data->quickbook_id;
		$company_corporation_name = $user_vendor_data->company_corporation_name;
		
		/*******make bill payable  *************/
		$quickbook_creds = $this->get_quickbook_creds();
		$quickbook_creds_arr = json_decode($quickbook_creds,true);
		if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && isset($quickbook_creds_arr['api_error'])) {
			$quickbook_invoice_res = $quickbook_creds_arr['api_error'];
		return \Response::json(["status" => 'error','message' => $quickbook_invoice_res, "type" => '']);
		} else {}
		if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr)) {
			$dataService = DataService::Configure($quickbook_creds_arr);
			$dataService->throwExceptionOnError(true);
			try {
				/*******  bank account ref can be get*******/
				/* $bill_payment_account_query = "select * from Account where AccountType = 'Bank' && AccountSubType  = 'Checking'";
				$bill_payment_account_search = $dataService->Query($bill_payment_account_query);
				foreach($bill_payment_account_search as $vals) {
					echo $vals->Id.' '.$vals->FullyQualifiedName.'<br>';
				} */
				/********get invoice related creds for quickbook************/
				 $quickbook_bill_payable_bank_id = 0;
				  $invoice_creds = $this->get_quickbook_invoice_creds();
				$invoice_creds_arr = json_decode($invoice_creds,true);
				if(isset($invoice_creds_arr) && !empty($invoice_creds_arr)) {
					$quickbook_bill_payable_bank_id = $invoice_creds_arr['quickbook_bill_payable_bank_id'];
				} else {}
				$BankAccountRef = array('value' => $quickbook_bill_payable_bank_id);
				 /********end get invoice related creds for quickbook ************/
				
				/*******  end bank account ref *******/
				$private_note = 'Bill number '.$bill_quickbook_invoice_id.' payment';
				$vendor_ref = array("value" => $vendor_quickbook_id);
				$theResourceObj = QuickbookBillPayment::create([
				  "VendorRef" => $vendor_ref,
				  "PayType" => "Check",
				  "CheckPayment" => [
				  "PrintStatus" => "NeedToPrint", 
					"BankAccountRef" => $BankAccountRef
				  ],
				  "TotalAmt" => $total_amount,
				  "PrivateNote" => $private_note,
				  "Line" => [
					[
					  "Amount" => $total_amount,
					  "LinkedTxn" => [
						[
						  "TxnId" => $bill_quickbook_invoice_id,
						  "TxnType" => "Bill"
						]
					  ]
					]
				  ]
				]);
				$resultingObj = $dataService->Add($theResourceObj);
				$error = $dataService->getLastError();
				if ($error) {
				$payable_quickbook_invoice_res = "The Status code is: " . $error->getHttpStatusCode() . "\n";
				$payable_quickbook_invoice_res .= "The Helper message is: " . $error->getOAuthHelperError() . "\n";
				$payable_quickbook_invoice_res .= "The Response message is: " . $error->getResponseBody() . "\n";
				}
				else {
				$payable_quickbook_invoice_id = $resultingObj->Id;
				}
			} catch (\Exception $e){
				$message = $e->getMessage();
				return \Response::json(["status" => 'error','message' => $message, "type" => '']);
			}
		}else {
			return \Response::json(["status" => 'error','message' => 'API creds is not Wrong/Inactive', "type" => '']);
		}
		
		/*******end make bill payable  *************/
		$ticket_id_arr = explode(",",$ticket_ids);
		foreach($ticket_id_arr as $id_data) {
		$where = array('id' => $id_data,'status' => 'completed');
		$update_completed = array('paid_to_employee' => 'completed');
		 DB::table('dispatch_tickets')
			->where($where)
			->update($update_completed);
		}
		/******update id or err in invoice ************/
		$invoice_data->quickbook_payable_bill_res = $payable_quickbook_invoice_res;
		$invoice_data->quickbook_payable_bill_id = $payable_quickbook_invoice_id;
		/******end update id or err in invoice ************/
		$date = Carbon::now();
		$invoice_data->status = 'completed';
		$invoice_data->completed_date = $date;
		$invoice_data->save();
		/******** debit employee amount */
		$user_last_amount = $user_total_amount = 0;
		$user_query = $get_user_trans = '';
		$user_type = 'employee';
		$trans_type = 'debit';
		$expense = $subtotal;
		$table_name = 'users';
		$get_user_trans = Transaction::where('user_id', $user_id)->where('user_type', $user_type)->orderBy('id', 'desc')->take(1)->get()->toArray();
		$user_query = User::where('id','=',$user_id)->get()->toArray();
		$user = $user_query[0];
		if(isset($get_user_trans) && !empty($get_user_trans)) {
							$user_last_amount = $get_user_trans[0]['total_amount'];
						}else {}
		$user_total_amount = $user_last_amount - $subtotal;
		$user_date = date('Ymdhis');
		 $default_transaction_number = 'JPGTN'.$user_date;
		 $user_tran_data = array(
		 'default_transaction_number' => $default_transaction_number,
		 'user_id' => $user_id,
		 'user_type' => $user_type,
		 'trans_genrate_type' => 'system',
		 'type' => $trans_type,
		 'invoice_id' => $id,
		 'amount' => $expense,
		 'total_amount' => $user_total_amount,
		 'message' => 'Invoice number :- '.$invoice_number.' paid',
		 );
		 $user_trans = Transaction::Create($user_tran_data);
	$user_where = array('id' => $user_id);
	$user_update = array('current_amount' => $user_total_amount);
	DB::table($table_name)
				->where($user_where)
				->update($user_update);
		/******** end debit user amount */
		return \Response::json(["status" => 'success','message' => 'completed', "type" => '']);
	 }
	 
}