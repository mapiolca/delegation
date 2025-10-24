

<?php
/* Copyright (C) 2020	Pierre Ardoin		<mapoiolca@me.com>

 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */


//Organization

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://thirdparty.qonto.com/v2/organizations/%7Bid%7D",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_POSTFIELDS => "{}",
	CURLOPT_HTTPHEADER => array(
	"authorization: ".$conf->global->LMDB_QONTO_SLUG.":".$conf->global->LMDB_QONTO_AUTHORIZATION.""
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  var_dump( "cURL Error #:" . $err);
  return 1;
} else {

$sanitized_iban = preg_replace("% %", "", $object->iban);

//var_dump($sanitized_iban);

//var_dump("response : ".$response);
	$retour = array(json_decode($response, true));

	//var_dump($retour);

	foreach ($retour as $type => $value) {

		$tableau = $retour[$type];

		//var_dump($tableau);

		foreach ($tableau as $transactions => $val) {
			
			$transaction = $tableau[$transactions];
			//var_dump($transaction); 

			foreach ($transaction as $field => $v) {

				$data = $transaction[$field];

				//var_dump($data);

				foreach ($data as $d => $v) {

					if ($sanitized_iban == $data[$d]['iban']) {

						$slug = $data[$d]['slug'];
						$iban = $data[$d]['iban'];
						$bic = $data[$d]['bic'];
						$currency = $data[$d]['currency'];
						$balance = $data[$d]['balance'];
						$balance_cents = $data[$d]['balance_cents'];
						$authorized_balance = $data[$d]['authorized_balance'];
						$authorized_balance_cents = $data[$d]['authorized_balance_cents'];

					}					
				}
			}
		}
	}
}

//Pagination

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://thirdparty.qonto.com/v2/transactions?iban=".$iban."&slug=".$conf->global->LMDB_QONTO_SLUG."&per_page=".$conf->global->LMDB_QONTO_PER_PAGE."&&current_page=".$page."&status[]=completed&status[]=declined&status[]=reversed&status[]=pending&sort_by=updated_at:desc",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_POSTFIELDS => "{}",
	CURLOPT_HTTPHEADER => array(
	"authorization: ".$conf->global->LMDB_QONTO_SLUG.":".$conf->global->LMDB_QONTO_AUTHORIZATION.""
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  print "cURL Error #:" . $err;

} else {

	$retour = array(json_decode($response, true));

	foreach ($retour as $type => $value) {

		$tableau = $retour[$type];

		foreach ($tableau as $transactions => $val) {

			$current_page = $tableau[$transactions]['current_page']; 
			$next_page = $tableau[$transactions]['next_page'];
			$prev_page = $tableau[$transactions]['prev_page'];
			$total_pages = $tableau[$transactions]['total_pages'];
			$local_amount = $tableau[$transactions]['local_amount'];
			$total_count = $tableau[$transactions]['total_count'];

			//var_dump($current_page);
			if ($total_pages >= 2) {
				$pagination = '<table class="centpercent notopnoleftnoright" style="margin-bottom: 6px; border: 0">';
					$pagination.= '<tbody>';
						$pagination.= '<tr>';
							$pagination.= '<td class="nobordernopadding valignmiddle">';
								$pagination.= '<div class="titre inline-block">Écritures bancaires ('.$total_count.')</div>';
							$pagination.= '</td>';
							$pagination.= '<td class="nobordernopadding valignmiddle right">';
							$pagination.='<a class="butAction" style="margin-bottom: 5px !important; margin-top: 5px !important" href="'.DOL_URL_ROOT.'/custom/delegation/tabs/compte.php?action=Qontoreconcile&iban='.$iban.'&page='.$page.'&id='.$accountid.'">'.$langs->trans("ConciliateQonto").'</a>';
							$pagination.= '</td>';
							$pagination.= '<td class="nobordernopadding valignmiddle right">';
								$pagination.= '<div class="pagination">';
									$pagination.= '<ul>';
									if (!is_null($prev_page)) {
										$pagination.= '<li class="pagination">';
											$pagination.= '<a class="paginationprev" href="/custom/delegation/tabs/compte.php?page='.$prev_page.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">';
												$pagination.= '<i class="fa fa-chevron-left" title="Précédent"></i>';
											$pagination.= '</a>';
										$pagination.= '</li>';
									}

									$pagination.= '<li class="pagination"></li>';

									if ($current_page == 2 ) {
														
										$pagination.= '<li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$prev_page.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$prev_page.'</a>';
										$pagination.= '</li>';
														
									}elseif ($current_page == 3) {

										$prev_page_1 = $prev_page - 1;

										$pagination.= '<li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$prev_page_1.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$prev_page_1.'</a>';
										$pagination.= '</li>';
										$pagination.= '<li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$prev_page.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$prev_page.'</a>';
										$pagination.= '</li>';
									}elseif ($current_page == 4) {

										$prev_page_1 = $prev_page - 1;
										$prev_page_2 = $prev_page - 2;

										$pagination.= ' <li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$prev_page_2.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$prev_page_2.'</a>';
										$pagination.= '</li>';

										$pagination.= '<li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$prev_page_1.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$prev_page_1.'</a>';
										$pagination.= '</li>';

										$pagination.= '<li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$prev_page.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$prev_page.'</a>';
										$pagination.= '</li>';
									}elseif ($current_page >= 5) {

										$prev_page_1 = $prev_page - 1;
										$prev_page_2 = $prev_page - 2;

										$pagination.= ' <li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page=1&amp;contextpage=banktransactionlist&amp;id='.$id.'">1...</a>';
										$pagination.= '</li>';

										$pagination.= '<li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$prev_page_2.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$prev_page_2.'</a>';
										$pagination.= '</li>';

										$pagination.= '<li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$prev_page_1.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$prev_page_1.'</a>';
										$pagination.= '</li>';

										$pagination.= '<li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$prev_page.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$prev_page.'</a>';
										$pagination.= '</li>';
									}

									$max_page = $total_pages - 4;

									if ($current_page <= $max_page) {
													
										for ($nombre_de_pages = $current_page; $nombre_de_pages <= $current_page+3; $nombre_de_pages++){

											if ($page == $nombre_de_pages) {
												$pagination.=  '<li class="pagination">';
													$pagination.= '<span class="active">'.$page.'</span>';
												$pagination.= '</li>';
											}else{
												$pagination.= ' <li class="pagination">';
													$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$nombre_de_pages.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$nombre_de_pages.'</a>';
												$pagination.= '</li>';
											}
										}

										if ($total_pages << 4) {
											$pagination.= ' <li class="pagination">';
												$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$total_pages.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">...'.$total_pages.'</a>';
											$pagination.= '</li>';
										}


										if ($next_page >= 2) {
											$pagination.= '<li class="pagination">';
												$pagination.= '<a class="paginationnext" href="/custom/delegation/tabs/compte.php?page='.$next_page.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">';
													$pagination.= '<i class="fa fa-chevron-right" title="Suivant"></i>';
												$pagination.= '</a>';
											$pagination.= '</li>';
										}
									}else{

										$next_page_1 = $next_page + 1;
										$next_page_2 = $next_page + 2;
										$pagination.= '<li class="pagination">';
											$pagination.= '<span class="active">'.$page.'</span>';
										$pagination.= '</li>';

										if ($next_page <= $total_pages && $next_page != null) {
											$pagination.= '<li class="pagination">';
												$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$next_page.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$next_page.'</a>';
											$pagination.= '</li>';
										}

										if ($next_page_1 <= $total_pages && $next_page != null) {
										$pagination.= '<li class="pagination">';
											$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$next_page_1.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$next_page_1.'</a>';
										$pagination.= '</li>';
										}

										if ($next_page_2 <= $total_pages && $next_page != null) {
											$pagination.= '<li class="pagination">';
												$pagination.= '<a href="/custom/delegation/tabs/compte.php?page='.$next_page_2.'&amp;contextpage=banktransactionlist&amp;id='.$id.'">'.$next_page_2.'</a>';
											$pagination.= '</li>';
										}							
									}
									$pagination.='</ul>';
								$pagination.= '</div>';
							$pagination.= '</td>';
						$pagination.= '</tr>';
					$pagination.= '</tbody>';
				$pagination.= '</table>';
			}	
		}
	}
}

?>


<?php

	/**
	 * 
	 */
	class Qonto extends CommonObject
	{
		
		function __construct($db)
		{
			$this->db = $db;
		}

		public function QontoTransactions($iban, $page, $accountid, $action)
		{
			global $conf, $langs, $user, $object;

			$curl = curl_init();

			$sanitized_iban = preg_replace("% %", "", $object->iban);

			//var_dump($sanitized_iban);

			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://thirdparty.qonto.com/v2/transactions?per_page=".$conf->global->LMDB_QONTO_PER_PAGE."&iban=".$sanitized_iban."&slug=".$conf->global->LMDB_QONTO_SLUG."&current_page=".$page."&status[]=completed&status[]=pending&sort_by=settled_at:desc",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_POSTFIELDS => "{}",
				CURLOPT_HTTPHEADER => array(
				"authorization: ".$conf->global->LMDB_QONTO_SLUG.":".$conf->global->LMDB_QONTO_AUTHORIZATION.""
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				echo "cURL Error #:" . $err;
				return 1;
			}else 
			{
				$this->db->begin();

				$retour = array(json_decode($response, true));
				foreach ($retour as $type => $value) {

					$tableau = $retour[$type];

					foreach ($tableau as $transactions => $val) {
						
						$transaction = $tableau[$transactions];
						$compte = "";
						foreach ($transaction as $field => $v) {

							$pattern = "/".$conf->global->LMDB_QONTO_SLUG."-".$object->array_options['options_lmdb_complement_slug_number']."-transaction-/";
							
							$pattern2 = "/".$conf->global->LMDB_QONTO_SLUG."-/";
							$transaction_ref_min = preg_replace($pattern2, "", $transaction[$field]['transaction_id']);
							$transaction_ref = strtoupper($transaction_ref_min);

							$this->transaction_id = preg_replace($pattern, "", $transaction[$field]['transaction_id']); 
							$this->amount = $transaction[$field]['amount'];
							$this->amount_cents = $transaction[$field]['amount_cents'];
							$this->attachment_ids = $transaction[$field]['attachment_ids'];
							$this->local_amount = $transaction[$field]['local_amount'];
							$this->local_amount_cents = $transaction[$field]['local_amount_cents'];
							$this->side = $transaction[$field]['side'];
							$this->operation_type = $transaction[$field]['operation_type'];
							$this->currency = $transaction[$field]['currency'];
							$this->local_currency = $transaction[$field]['local_currency'];
							$this->label = $transaction[$field]['label'];
							$this->settled_at = $transaction[$field]['settled_at'];
							$this->emitted_at = $transaction[$field]['emitted_at'];
							$this->updated_at = $transaction[$field]['updated_at'];
							$this->status = $transaction[$field]['status'];
							$this->note = $transaction[$field]['note'];
							$this->reference = $transaction[$field]['reference'];
							$this->vat_amount = $transaction[$field]['vat_amount'];
							$this->vat_amount_cents = $transaction[$field]['vat_amount_cents'];
							$this->vat_rate = $transaction[$field]['vat_rate'];
							$this->initiator_id = $transaction[$field]['initiator_id'];
							$this->label_ids = $transaction[$field]['label_ids'];
							$this->attachment_lost = $transaction[$field]['attachment_lost'];
							$this->attachment_required = $transaction[$field]['attachment_required'];

							$type_impot_TVA = substr($reference, 0, 3);
							$type_impot_IS = substr($reference, 0, 3);

							if ($this->side == "debit") {$amount_debit = "-".price($this->amount);}else{ $amount_debit = "";}
							if ($this->side == "credit") {$amount_credit = "+".price($this->amount);}else{ $amount_credit = "";}

							$this->compte.= '<tr>
										<td class="maxwidth50">
											'.$this->transaction_id.'
										</td>
										<td class="minwidth100">
											'.dol_print_date($this->settled_at, "day").'
										</td>
										<td class="maxwidth500">
											'.$this->label.'
										</td>
										<td class="maxwidth500">
											'.$this->reference.'
										</td>
										<td class="minwidth100">
											'.$amount_debit.'
										</td>
										<td class="minwidth100">
											'.$amount_credit.'
										</td>
										<td class="minwidth100">
											'.$langs->trans($this->operation_type).'
										</td>
										<td class="minwidth100">
											'.$langs->trans($this->status).'
										</td>
										<td class="maxwidth700">
											'.$this->note.'
										</td>
								</tr>
							';
												
							if ($action == 'Qontoreconcile') {

								//print "rapprochement effectué";
								$tiers = $this->label;

								if ($this->operation_type == "transfer"){
									$paiementid = "2";
									$type_paiement = "VIR";
								}elseif ($this->operation_type == "income"){
									$paiementid = "2";
									$type_paiement = "VIR";
								}elseif ($this->operation_type == "card") {
									$paiementid = "6";
									$type_paiement = "CB";
								}elseif ($this->operation_type == "qonto_fee"){
									$paiementid = "3";
									$type_paiement = "PRE";
								}elseif ($this->operation_type == "direct_debit"){
									$paiementid = "3";
									$type_paiement = "PRE";
								}

								if ($this->side == "debit") 
								{
									$amount_value = "-".$this->amount;
								}else
								{
								 	$amount_value = "+".$this->amount;
								}

								$num_paiement = $transaction_ref ;

								$amount = $this->amount;

								$reference = $this->reference;

								$settled_at = $this->settled_at;

								$emitted_at = $this->emitted_at;

								$note = $this->note;

								$side = $this->side ;

			
								$qonto  = new QONTOFunction($this->db);

								if (($this->operation_type == "transfer" || $this->operation_type == "card" || $this->operation_type == "qonto_fee" || $this->operation_type == "direct_debit") && $this->status == "completed" && $object->array_options['options_lmdb_complement_slug_number'] == $num_paiement[0])
								{
									
									$supplier = $qonto->fetch_supplier($tiers, $settled_at);

									if ($qonto->supplier_ok == '1') {
										
										$fourn_id = $qonto->id;

										$ref = $qonto->ref;

										$facture_fourn = $qonto->fetch_supplier_invoice($fourn_id, $amount, $reference, $settled_at, $note, $transaction_ref, $ref);

										$facid = $qonto->facid ;

										$thirdparty_name = $qonto->name;

										$releve_facid     = $qonto->releve_facid;
										$releve_total_ttc = $qonto->releve_total_ttc;
										$ff_ok 			  = $qonto->ff_ok;
										$fc_ok			  = $qonto->fc_ok;
										$alertcreatemessage[] = $qonto->alertcreatemessage; 

										if ($ff_ok == '1') {

											if ($qonto->rappro == '1') {
												$rappro_facid_fourn = $qonto->QontoRapproWithFacid($fc_ok, $ff_ok, $side, $amount, $releve_facid, $releve_total_ttc, $settled_at, $emitted_at);
												
												if ($qonto->result_rappro == '1') {
													$nb_rappro[] = $qonto->result_rappro ;
												}
											}elseif ($qonto->rappro == '2') {
												$rappro_transaction_fourn = $qonto->QontoRapproWithTransaction($transaction_ref, $side, $amount, $fc_ok, $ff_ok, $settled_at, $emitted_at);
												
												if ($qonto->result_rappro == '1') {
													$nb_rappro[] = $qonto->result_rappro ;
												}
											}

										}elseif ($ff_ok == '0') {

											if ( !empty($qonto->facid)) {

												$create = $qonto->create_paiement_fourn($ref, $settled_at, $emitted_at, $amount, $note, $num_paiement, $accountid, $facid, $paiementid, $type_paiement, $amount_value, $fourn_id, $thirdparty_name);
												
											}
											if ($qonto->result_create_fourn == '1') {
												$nb_create_fourn[] = $qonto->result_create_fourn ;
											}

											if ($qonto->result == '-2') {
												echo ($message ? dol_htmloutput_mesg($message, '', ($error ? 'error' : 'ok'), 0) : '');
											}
										}
									}
									else
									{
										$ff_ok = '1';
										$rappro_transaction_fourn = $qonto->QontoRapproWithTransaction($transaction_ref, $side, $amount, $fc_ok, $ff_ok, $settled_at, $emitted_at);
										if ($qonto->result_rappro == '1') {
											$nb_rappro[] = $qonto->result_rappro ;
										}
									}	
								}elseif ($this->operation_type == "income" && $this->status == "completed") {
									
									$customer = $qonto->fetch_customer($tiers, $settled_at);

									if ($qonto->client_ok == '1') {
									
										$client_id = $qonto->id;

										$ref = $qonto->ref;

										$facture_client = $qonto->fetch_customer_invoice($client_id, $amount, $reference, $settled_at, $note, $transaction_ref, $ref);

										$facid = $qonto->facid ;

										$thirdparty_name = $qonto->name;

										$releve_facid     = $qonto->releve_facid;
										$releve_total_ttc = $qonto->releve_total_ttc;
										$ff_ok 			  = $qonto->ff_ok;
										$fc_ok			  = $qonto->fc_ok;
										$alertcreatemessage[] = $qonto->alertcreatemessage; 

										if ($fc_ok == '1'){

											if ($qonto->rappro == '1') {
												$rappro_facid_client = $qonto->QontoRapproWithFacid($fc_ok, $ff_ok, $side, $amount, $releve_facid, $releve_total_ttc, $settled_at, $emitted_at);
												
												if ($qonto->result_rappro == '1') {
													$nb_rappro[] = $qonto->result_rappro ;
												}
											}elseif ($qonto->rappro == '2') {
												$rappro_transaction_client = $qonto->QontoRapproWithTransaction($transaction_ref, $side, $amount, $fc_ok, $ff_ok, $settled_at, $emitted_at);
												
												if ($qonto->result_rappro == '1') {
													$nb_rappro[] = $qonto->result_rappro ;
												}
											}
										}elseif ($fc_ok == '0') {

											$create = $qonto->create_paiement_client($ref, $settled_at, $emitted_at, $amount, $note, $num_paiement, $accountid, $facid, $paiementid, $type_paiement, $amount_value, $client_id, $thirdparty_name);
												
											if ($qonto->result_create_client == '1') {
												$nb_create_client[] = $qonto->result_create_client;
											}

											if ($qonto->result_rappro == '1') {
												$nb_rappro[] = $qonto->result_rappro ;
											}

											if ($qonto->result == '-2') {
												echo ($message ? dol_htmloutput_mesg($message, '', ($error ? 'error' : 'ok'), 0) : '');
											}

										}
									}
									else
									{
										$fc_ok = '1';
										$rappro_transaction_fourn = $qonto->QontoRapproWithTransaction($transaction_ref, $side, $amount, $fc_ok, $ff_ok, $settled_at, $emitted_at);
													
										if ($qonto->result_rappro == '1') {
											$nb_rappro[] = $qonto->result_rappro ;
										}
									}
								}
								$informations_qonto ='<div class="info"><b>'.$langs->trans('Qonto_info').'</b><br>'.$langs->trans('Qonto_info_text').'</div>';
							}							
						}
						$nb_rappro_val = count($nb_rappro);
						$nb_create_fourn_val = count($nb_create_fourn);
						$nb_create_client_val = count($nb_create_client);
						$nb_alertcreatemessage = count($alertcreatemessage);
						$alertcreatemessage_client = '';
						for ($nb_alert=0; $nb_alert < $nb_alertcreatemessage; $nb_alert++) { 
							$alertcreatemessage_client.= $alertcreatemessage[$nb_alert];
						}
						if ($nb_create_fourn_val  == '0' && $nb_rappro_val == '0' && $nb_create_client_val == '0' && $action == 'Qontoreconcile') {
							
								$alert ='<div class="warning"><b>'.$langs->trans('Qonto_warning').'</b><br>'.$langs->trans('Qonto_warning_text').'</div>';

						}else{

							if ($nb_create_fourn_val == '1') {
								$alert ='<div class="info"><b>'.$langs->trans('Qonto_create_payement_fourn').'</b> '.$nb_create_fourn_val.' '.$langs->trans('Qonto_create_payement_text1').'</div>';
							}elseif ($nb_create_fourn_val >> '1') {
								$alert ='<div class="info"><b>'.$langs->trans('Qonto_create_payement_fourn').'</b> '.$nb_create_fourn_val.' '.$langs->trans('Qonto_create_payement_text2').'</div>';
							}
							if ($nb_create_client_val == '1') {
								$alert ='<div class="info"><b>'.$langs->trans('Qonto_create_payement_client').'</b> '.$nb_create_client_val.' '.$langs->trans('Qonto_create_payement_text1').'</div>';
							}elseif ($nb_create_client_val >> '1') {
								$alert ='<div class="info"><b>'.$langs->trans('Qonto_create_payement_client').'</b> '.$nb_create_client_val.' '.$langs->trans('Qonto_create_payement_text2').'</div>';
							}
							if ($nb_rappro_val == '1') {
								$alert ='<div class="info"><b>'.$langs->trans('Qonto_rappro').'</b> '.$nb_rappro_val.' '.$langs->trans('Qonto_rappro_text1').'</div>';
							}elseif ($nb_rappro_val >> '1') {
								$alert ='<div class="info"><b>'.$langs->trans('Qonto_rappro').'</b> '.$nb_rappro_val.' '.$langs->trans('Qonto_rappro_text2').'</div>';
							}
							if ($alertcreatemessage_client !== '') {
								$alert.='<div class="warning"><b>Attention :</b>'.$alertcreatemessage_client.'</div>';
							}

						}
						print $informations_qonto;
						print $alert;
						$nb_rappro_val = '0';
						$nb_create_fourn_val = '0';
						$nb_create_client_val = '0';
						return 1;
					}

				}
				
			}
			
		}

	}
	/**
	 *	\class      	DC4Line
	 *	\brief      	Class to manage margins
	 *	\remarks		Uses lines of llx_DC4_deleg_csst tables
	 */

	class QONTOFunction
	{
	   /**
		*  \brief  Constructeur de la classe
		*  @param  DB          handler acces base de donnees
		*/
	    function QONTOFunction($db)
	    {
	        $this->db = $db;
	    }

		public function fetch_supplier($tiers, $settled_at)
		{

			global $conf, $langs, $object, $user;

			$date_paiement = date_create($settled_at);
			
			$jour = date_format($date_paiement,"d");
			$mois = date_format($date_paiement,"m");
			$annee = date_format($date_paiement,"Y");

			//print dol_print_date($nownotime);

			if (empty($tiers)) return -1;

			$sql = 'SELECT s.rowid, s.nom as name, s.name_alias, s.fournisseur';
			$sql .= ' FROM '.MAIN_DB_PREFIX.'societe as s';
			$sql .= ' WHERE (s.nom LIKE "%'.$tiers.'%" OR s.name_alias LIKE "%'.$tiers.'%")';
			$sql .= ' AND s.fournisseur = "1"';

			$resql = $this->db->query($sql);

			if ($resql)
			{
				$num = $this->db->num_rows($resql);

				if ($num > 1)
				{
					$this->error = 'Fetch found several records. Rename one of thirdparties to avoid duplicate.';
					dol_syslog($this->error, LOG_ERR);
					$result = -2;
				}
				elseif ($num)   // $num = 1
				{
					$obj = $this->db->fetch_object($resql);

					$this->id           	= $obj->rowid;
					$this->entity       	= $obj->entity;

					$this->ref          	= $obj->rowid;
					$this->name 			= $obj->name;
					$this->nom          	= $obj->name; // deprecated
					$this->name_alias 		= $obj->name_alias;

					$this->parent 			= $obj->parent;

					$this->code_client 		= $obj->code_client;
					$this->code_fournisseur = $obj->code_fournisseur;

					$this->supplier_ok = $obj->fournisseur ;

					require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.class.php';
					require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';

					$datepaye = dol_mktime(12, 0, 0, $mois, $jour, $annee);

					// Creation de la ligne paiement
					$paiement = new PaiementFourn($this->db);
					$paiement->datepaye     = $datepaye;

					$this->ref = $paiement->getNextNumRef($this->id);

					return 1;

				}
				else
				{
					return 0; 
				}
			}
		}

		public function fetch_supplier_invoice($fourn_id, $amount, $reference, $settled_at, $note, $transaction_ref, $ref)
		{

			global $conf, $langs, $object, $user;

			if (empty($fourn_id)) return -1;

			$date_paiement = date_create($settled_at);
			$mois = date_format($date_paiement,"m");
			$annee = date_format($date_paiement,"Y");

			$sql = "SELECT rowid, ref_supplier, ref, libelle, MONTH (date_lim_reglement) as mois, YEAR (date_lim_reglement) as annee FROM ".MAIN_DB_PREFIX."facture_fourn WHERE fk_soc = ".$fourn_id;
			$sql.= " AND total_ttc = ".$amount;
			$sql.= " AND entity = ".$conf->entity;
			$sql.= " AND MONTH(date_lim_reglement) = ".$mois;
			$sql.= " AND YEAR(date_lim_reglement) = ".$annee;
			$sql.= " AND paye = '0' "; 
				
			$reponse = $this->db->query($sql);
 			
			$donnees = $this->db->fetch_object($reponse);
			
			$num = $this->db->num_rows($reponse);

			for ($i=0; $i < $num; $i++) { 
				if(strpos($donnees->ref_supplier, $reference) !== false){

					$reference = $donnees->ref_supplier;

				}elseif(strpos($donnees->libelle, $reference) !== false){

					$reference = $donnees->libelle;

				}elseif(strpos($donnees->ref, $reference) !== false){

					$reference = $donnees->ref;

				}else{

					if(strpos($reference, $donnees->ref_supplier) !== false)
					{	//print $sql."<br>";
						//print $donnees->ref_supplier.' trouvé dans '.$reference.'<br>';
						$reference = $donnees->ref_supplier;
					}elseif(strpos($reference, $donnees->ref) !== false)
					{	//print $sql."<br>";
						//print $donnees->ref_supplier.' trouvé dans '.$reference.'<br>';
						$reference = $donnees->ref;
					}elseif(strpos($reference, $donnees->libelle) !== false)
					{	//print $sql."<br>";
						//print $donnees->ref_supplier.' trouvé dans '.$reference.'<br>';
						$reference = $$donnees->ref; 
					}elseif(strpos($note, $donnees->ref) !== false)
					{	//print $sql."<br>";
						//print $donnees->ref_supplier.' trouvé dans '.$reference.'<br>';
						$reference = $donnees->ref; 
					}else{
						$reference = $donnees->ref;
						$pattern3 = '/'.$object->array_options['options_lmdb_complement_slug_number'].'-TRANSACTION-/';
						$ecriture_id = preg_replace($pattern3, "", $transaction_ref);
						$link_ref = '<a href="../../../../fourn/paiement/card.php?ref='.$ref.'">'.$ref.'</a>';
						$link_reference = '<a href="../../../../fourn/facture/card.php?ref='.$reference.'">'.$reference.'</a>';
						$alertcreatemessage = '<br>'.$langs->trans('Qonto_alertcreatemessage_fourn', $link_ref, $link_reference, $ecriture_id);
					}
				}
			}

			// On séléctionne une facture fournisseur pouvant contenir la référence, et ayant le même montant, total TTC, appartenant cette entité et n'atant pas classée comme payée. 
			$sql = "SELECT ff.rowid as ff_id, ff.fk_soc as supplier, ff.ref_supplier,  ff.total_ttc as amount, ff.datef as echeance, ff.paye as status";
			$sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn as ff";
			$sql.= " WHERE (ff.ref_supplier LIKE '%".$reference."%' OR ff.ref LIKE '%".$reference."%' OR ff.libelle LIKE '%".$reference."%')";
			$sql.= " AND ff.fk_soc = ".$fourn_id;
			$sql.= " AND ff.total_ttc = ".$amount;
			$sql.= " AND ff.entity = ".$conf->entity;
			$sql.= " AND ff.paye = '0' ";

			$resql = $this->db->query($sql);


			if ($resql)
			{
				// On compte le nombre de résultat
				$num = $this->db->num_rows($resql);

				if ($num > 1) //Si il y a plus de un résultat, on retourne un message d'erreur.
				{
					$this->error = $langs->trans('Fetch found several records. Rename one of thirdparties to avoid duplicate.');
					dol_syslog($this->error, LOG_ERR);
					$result = -2;
				}
				elseif ($num == '1') // Si il n'y a qu'un seul résultat on séléctionne les informations nécessaires de cette facture. Et on les fait remonter.
				{
					$obj = $this->db->fetch_object($resql);

					$this->facid = $obj->ff_id;
					$this->ff_entity = $obj->entity;

					$this->ff_ref_supplier = $obj->ref_supplier;
					$this->ff_libelle = $obj->libelle;

					$this->ff_total_ttc = $obj->amount;

					$this->ff_ok = $obj->status;

					return 1;

				}
				elseif ($num == '0') //Si il n'y a pas de résultat, on cherche en fonction de la référence fournisseur, la date limite de règlement, etc.
				{	
					$this->ff_ok = '1';

					$sql = "SELECT rowid, ref_supplier, ref, libelle, MONTH (date_lim_reglement) as mois, YEAR (date_lim_reglement) as annee FROM ".MAIN_DB_PREFIX."facture_fourn WHERE fk_soc = ".$fourn_id;
					$sql.= " AND total_ttc = ".$amount;
					$sql.= " AND entity = ".$conf->entity;
					$sql.= " AND MONTH(date_lim_reglement) = ".$mois;
					$sql.= " AND YEAR(date_lim_reglement) = ".$annee;
					$sql.= " AND paye = '1' "; 

					$reponse = $this->db->query($sql);
		 			
					$donnees = $this->db->fetch_object($reponse);
					
					$num = $this->db->num_rows($reponse);

					for ($i=0; $i < $num; $i++) { 

						if(strpos($donnees->ref_supplier, $reference) !== false){

							$reference = $donnees->ref_supplier;

						}elseif(strpos($donnees->libelle, $reference) !== false){

							$reference = $donnees->libelle;

						}elseif(strpos($donnees->ref, $reference) !== false){

							$reference = $donnees->ref;

						}else{

							if(strpos($reference, $donnees->ref_supplier) !== false)
							{	//print $sql."<br>";
								//print $donnees->ref_supplier.' trouvé dans '.$reference.'<br>';
								$reference = $donnees->ref_supplier;
							}else{
								//print $donnees->ref_supplier.' n\'a pas été trouvé dans '.$reference.'. <br>';
							}
							if(strpos($reference, $donnees->ref) !== false)
							{	//print $sql."<br>";
								//print $donnees->ref_supplier.' trouvé dans '.$reference.'<br>';
								$reference = $donnees->ref;
							}else{
								//print $donnees->ref_supplier.' n\'a pas été trouvé dans '.$reference.'. <br>';
							}
							if(strpos($reference, $donnees->libelle) !== false)
							{	//print $sql."<br>";
								//print $donnees->ref_supplier.' trouvé dans '.$reference.'<br>';
								$reference = $$donnees->ref; 
							}else{
								//print $donnees->ref_supplier.' n\'a pas été trouvé dans '.$reference.'. <br>';
							}
							if(strpos($note, $donnees->ref) !== false)
							{	//print $sql."<br>";
								//print $donnees->ref_supplier.' trouvé dans '.$reference.'<br>';
								$reference = $donnees->ref; 
							}else{
								//print $donnees->ref_supplier.' n\'a pas été trouvé dans '.$reference.'. <br>';
							}
						}
					}

					$sql = "SELECT ff.rowid as ff_id, ff.fk_soc as supplier, ff.ref_supplier,  ff.total_ttc as amount, ff.datef as echeance, ff.paye as status";
					$sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn as ff";
					$sql.= " WHERE (ff.ref_supplier LIKE '%".$reference."%' OR ff.ref LIKE '%".$reference."%' OR ff.libelle LIKE '%".$reference."%')";
					$sql.= " AND ff.fk_soc = ".$fourn_id;
					$sql.= " AND ff.total_ttc = ".$amount;
					$sql.= " AND ff.entity = ".$conf->entity;
					$sql.= " AND ff.paye = '1' ";

					$resql = $this->db->query($sql);

					if ($resql)
					{
						$num = $this->db->num_rows($resql);

						if ($num > 1)
						{
							$this->error = $langs->trans('Fetch found several records. Rename one of thirdparties to avoid duplicate.');
							dol_syslog($this->error, LOG_ERR);
							$result = -2;
						}
						elseif ($num == '1')   // $num = 1
						{
							$obj = $this->db->fetch_object($resql);

							$this->releve_facid           	= $obj->ff_id;
							$this->releve_total_ttc 		= $obj->amount;
							$this->rappro 					= '1'; // Lorque $num = 1 on cherche à rapprocher via les données de la facture.
							$this->alertcreatemessage = $alertcreatemessage;

							return 1;

						}elseif($num == '0'){

							//$this->fc_ok  = '1';
							$this->rappro = '2'; // Lorque $num = 0 on cherche à rapprocher via la référence de transaction.
							return 1;
						}
						else
						{	
							$this->ff_ok = '1';
							return 0;
						}
					}
				}
			}
		}

		public function create_paiement_fourn($ref, $settled_at, $emitted_at, $amount, $note, $num_paiement, $accountid, $facid, $paiementid, $type_paiement, $amount_value, $fourn_id, $thirdparty_name)
		{
			global $conf, $langs, $object, $user;

			$date_valeur = date_create($settled_at);
			$date_operation = date_create($emitted_at);
			$datev = date_format($date_valeur,"Y-m-d");
			$dateo = date_format($date_operation,"Y-m-d");
			$releve_annee = date_format($date_valeur,"Y");
			$releve_mois = date_format($date_valeur,"m");

			$now=dol_now();

			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'bank (';
			$sql.= 'datec, tms, datev, dateo, amount, label, fk_account, fk_user_author, num_chq, num_releve, fk_user_rappro, rappro, fk_type)';
			$sql.= " VALUES ('".$this->db->idate($now)."'";
			$sql.= ", '".$this->db->idate($now)."'";
			$sql.= ", '".$datev."'";
			$sql.= ", '".$dateo."'";
			$sql.= ", '".$amount_value."'";
			$sql.= ", '(SupplierInvoicePayment)'";
			$sql.= ", '".$accountid."'";
			$sql.= ", ".$user->id;
			$sql.= ", '".$num_paiement."'";
			$sql.= ", '".$releve_annee."".$releve_mois."'";
			$sql.= ", ".$user->id;
			$sql.= ", '1'";
			$sql.= ", '".$type_paiement."')";

			//$printsql = "1";

			if ($printsql =='1') 
			{
				print $sql.'<br>';

			}else{
				
				$resql = $this->db->query($sql);
			}

			$bank_line_id = $this->db->last_insert_id(MAIN_DB_PREFIX.'bank');

			if ($resql || $printsql == "1") {

				$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'paiementfourn (';
				$sql.= 'ref, entity, datec, datep, amount, multicurrency_amount, fk_paiement, num_paiement, note, fk_user_author, fk_bank)';
				$sql.= " VALUES ('".$ref."', ".$conf->entity.", '".$this->db->idate($now)."',";
				$sql.= " '".$this->db->idate($date)."', '".$amount."', '".$amount."', ".$paiementid.", '".$this->db->escape($num_paiement)."', '".$this->db->escape($note)."', ".$user->id.", ".$bank_line_id.")";

				if ($printsql =='1') 
				{
					print $sql.'<br>';

				}else{
					
					$resql = $this->db->query($sql);
				}

				if ($resql || $printsql == "1") {

					$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX.'paiementfourn');
					
					$url = "/fourn/paiement/card.php?id=";
					$label = '(paiement)';
					$type = "payment_supplier";

					$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'bank_url (';
					$sql.= 'fk_bank, url_id, url, label, type)';
					$sql.= " VALUES ('".$bank_line_id."'";
					$sql.= ", '".$this->id."'";
					$sql.= ", '".$url."'";
					$sql.= ", '".$label."'";
					$sql.= ", '".$type."')";				
				
					if ($printsql =='1') 
					{
						print $sql.'<br>';

					}else{
						
						$resql = $this->db->query($sql);
					}

					if ($resql || $printsql == "1") {

						$url = "/fourn/card.php?socid=";
						$label = '(paiement)';
						$type = "company";

						$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'bank_url (';
						$sql.= 'fk_bank, url_id, url, label, type)';
						$sql.= " VALUES ('".$bank_line_id."'";
						$sql.= ", '".$fourn_id."'";
						$sql.= ", '".$url."'";
						$sql.= ", '".$thirdparty_name."'";
						$sql.= ", '".$type."')";

						if ($printsql =='1') 
						{
							print $sql.'<br>';

						}else{

							$resql = $this->db->query($sql);
						}
					
						if ($resql || $printsql == "1") {

							if (is_numeric($amount) && $amount <> 0)
							{
								$amount = price2num($amount);
								$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'paiementfourn_facturefourn (fk_facturefourn, fk_paiementfourn, amount, multicurrency_amount)';
								$sql .= ' VALUES ('.$facid.','. $this->id.',\''.$amount.'\', \''.$amount.'\')';

								if ($printsql =='1') 
								{
									print $sql.'<br>';

								}else{
									
									$resql = $this->db->query($sql);
								}

								if ($resql || $printsql == "1") {

									$sql = "UPDATE ".MAIN_DB_PREFIX."facture_fourn SET paye = 1 , fk_statut = 2 WHERE rowid = ".$facid;

									if ($printsql =='1')
									{
										print $sql.'<br><br>';

									}else{

										$resql = $this->db->query($sql);
									}
									
									if ($resql) {
										$this->db->commit();

										$ref='0'; 
										$settled_at='0'; 
										$emitted_at='0'; 
										$amount='0'; 
										$note='0'; 
										$num_paiement='0'; 
										$accountid='0'; 
										$facid='0'; 
										$paiementid='0'; 
										$type_paiement='0'; 
										$amount_value='0'; 
										$fourn_id='0'; 
										$thirdparty_name='0';
										$this->result_create_fourn = '1';
										$this->result_rappro == '1';

										return 1 ;
									}else{
										$this->error = $langs->trans('Qonto_commit_error');
										dol_syslog($this->error, LOG_ERR);
										$result = -2;
									}
								}else{
									$this->error = $langs->trans('Qonto_set_fourn_invoice_to_payed_error');
									dol_syslog($this->error, LOG_ERR);
									$result = -2;
								}
							}
						}else{
							$this->error = $langs->trans('Qonto_Cannot_insert_link_between_facture_fourn_and_paiement_fourn');
							dol_syslog($this->error, LOG_ERR);
							$result = -2;
						}
					}else{
						$this->error = $langs->trans('Qonto_Cannot_insert_bank_url');
						dol_syslog($this->error, LOG_ERR);
						$result = -2;
					}
				}else{
					$this->error = $langs->trans('Qonto_Cannot_insert_payment_into_payment_fourn_table');
					dol_syslog($this->error, LOG_ERR);
					$result = -2;
				}
			}else{
				$this->error = $langs->trans('Qonto_Cannot_insert_bank_entry_into_bank_table');
				dol_syslog($this->error, LOG_ERR);
				$result = -2;
			}
		}
		
		public function fetch_customer($tiers, $settled_at)
		{

			global $conf, $langs, $object, $user;

			$date_paiement = date_create($settled_at);
			
			$jour = date_format($date_paiement,"d");
			$mois = date_format($date_paiement,"m");
			$annee = date_format($date_paiement,"Y");

			if (empty($tiers)) return -1;

			$sql = 'SELECT s.rowid, s.nom as name, s.name_alias, s.client';
			$sql .= ' FROM '.MAIN_DB_PREFIX.'societe as s';
			$sql .= ' WHERE (s.nom LIKE "%'.$tiers.'%" OR s.name_alias LIKE "%'.$tiers.'%")';
			$sql .= ' AND s.client = "1"';

			$resql = $this->db->query($sql);

			if ($resql)
			{
				$num = $this->db->num_rows($resql);

				if ($num > 1)
				{
					$this->error = 'Fetch found several records. Rename one of thirdparties to avoid duplicate.';
					dol_syslog($this->error, LOG_ERR);
					$result = -2;
				}
				elseif ($num)   // $num = 1
				{
					$obj = $this->db->fetch_object($resql);

					$this->id           	= $obj->rowid;
					$this->entity       	= $obj->entity;

					$this->ref          	= $obj->rowid;
					$this->name 			= $obj->name;
					$this->nom          	= $obj->name; // deprecated
					$this->name_alias 		= $obj->name_alias;

					$this->parent 			= $obj->parent;

					$this->code_client 		= $obj->code_client;
					$this->code_fournisseur = $obj->code_fournisseur;

					$this->client_ok = $obj->client ;

					require_once DOL_DOCUMENT_ROOT.'/compta/paiement/class/paiement.class.php';

					$datepaye = dol_mktime(12, 0, 0, $mois, $jour, $annee);

					// Creation de la ligne paiement
					$paiement = new Paiement($this->db);
					$paiement->datepaye = $datepaye;

					$this->ref = $paiement->getNextNumRef($this->id);

					return 1;

				}
				else
				{
					return 0; 
				}
			}
		}

		public function fetch_customer_invoice($client_id, $amount, $reference, $settled_at, $note, $transaction_ref, $ref)
		{

			global $conf, $langs, $object, $user;

			if (empty($client_id)) return -1;

			$date_paiement = date_create($settled_at);
			$mois = date_format($date_paiement,"m");
			$annee = date_format($date_paiement,"Y");

			$sql = "SELECT rowid, ref, MONTH (date_lim_reglement) as mois, YEAR (date_lim_reglement) as annee FROM ".MAIN_DB_PREFIX."facture WHERE fk_soc = ".$client_id;
			$sql.= " AND total_ttc = ".$amount;
			$sql.= " AND entity = ".$conf->entity;
			$sql.= " AND MONTH(date_lim_reglement) = ".$mois;
			$sql.= " AND YEAR(date_lim_reglement) = ".$annee;
			$sql.= " AND paye = '0' ";
			$sql.= " AND fk_statut = '1' "; 
				
			$reponse = $this->db->query($sql);
 			
			$donnees = $this->db->fetch_object($reponse);
			
			$num = $this->db->num_rows($reponse);

			for ($i=0; $i < $num; $i++) { 

				if(strpos($donnees->ref, $reference) !== false){

					$reference = $donnees->ref;

				}elseif(strpos($donnees->ref_client, $reference) !== false){

					$reference = $donnees->ref;


				}else{

					if(strpos($reference, $donnees->ref) !== false)
					{	//print $sql."<br>";
						//print $donnees->ref.' trouvé dans '.$reference.'<br>';
						$reference = $donnees->ref;

					}elseif(strpos($note, $donnees->ref) !== false)
					{	//print $sql."<br>";
						//print $donnees->ref.' trouvé dans '.$note.'<br>';
						$reference = $donnees->ref;

					}elseif(strpos($reference, $donnees->ref_client) !== false)
					{	//print $sql."<br>";
						//print $donnees->ref.' trouvé dans '.$note.'<br>';
						$reference = $donnees->ref;

					}else{
						$reference = $donnees->ref;
						$pattern3 = '/'.$object->array_options['options_lmdb_complement_slug_number'].'-TRANSACTION-/'; 
						$ecriture_id = preg_replace($pattern3, "", $transaction_ref);
						$link_ref = '<a href="../../../../compta/paiement/card.php?ref='.$ref.'">'.$ref.'</a>';
						$link_reference = '<a href="../../../../compta/facture/card.php?ref='.$reference.'">'.$reference.'</a>';
						$alertcreatemessage = '<br>'.$langs->trans('Qonto_alertcreatemessage_client', $link_ref, $link_reference, $ecriture_id);
					}
				}
			}

			$sql = "SELECT fc.rowid as fc_id, fc.fk_soc as customer, fc.ref as ref,  fc.total_ttc as amount, fc.datef as echeance, fc.paye as status";
			$sql.= " FROM ".MAIN_DB_PREFIX."facture as fc";
			$sql.= " WHERE fc.ref LIKE '%".$reference."%'";
			$sql.= " AND fc.fk_soc = ".$client_id;
			$sql.= " AND fc.total_ttc = ".$amount;
			$sql.= " AND fc.entity = ".$conf->entity;
			$sql.= " AND fc.paye = '0' ";
			$sql.= " AND fc.fk_statut = '1' "; 

			$resql = $this->db->query($sql);

			if ($resql)
			{
				$num = $this->db->num_rows($resql);

				if ($num > 1)
				{
					$this->error = $langs->trans('Fetch found several records. Rename one of thirdparties to avoid duplicate.');
					dol_syslog($this->error, LOG_ERR);
					$result = -2;
				}
				elseif ($num == '1')   // $num = 1
				{
					$obj = $this->db->fetch_object($resql);

					$this->facid = $obj->fc_id;
					$this->fc_entity = $obj->entity;

					$this->fc_ref = $obj->ref;
					$this->fc_libelle = $obj->libelle;

					$this->fc_total_ttc = $obj->amount;

					$this->fc_ok = $obj->status;

					$this->alertcreatemessage = $alertcreatemessage;

					return 1;

				}
				elseif ($num == '0')
				{	
					$this->fc_ok = '1';

					//return 0;

					$sql = "SELECT rowid, ref, MONTH (date_lim_reglement) as mois, YEAR (date_lim_reglement) as annee FROM ".MAIN_DB_PREFIX."facture WHERE fk_soc = ".$client_id;
					$sql.= " AND total_ttc = ".$amount;
					$sql.= " AND entity = ".$conf->entity;
					$sql.= " AND MONTH(date_lim_reglement) = ".$mois;
					$sql.= " AND YEAR(date_lim_reglement) = ".$annee;
					$sql.= " AND paye = '1' ";
					$sql.= " AND fk_statut = '2' ";

					$reponse = $this->db->query($sql);
		 			
					$donnees = $this->db->fetch_object($reponse);
					
					$num = $this->db->num_rows($reponse);

					for ($i=0; $i < $num; $i++) { 
						if(strpos($donnees->ref, $reference) !== false)
						{
							$reference = $donnees->ref;
						}
						elseif(strpos($donnees->ref_client, $reference) !== false)
						{
							$reference = $donnees->ref;
						}
						else{

							if(strpos($reference, $donnees->ref) !== false)
							{	
								$reference = $donnees->ref;
							}

							if(strpos($note, $donnees->ref) !== false)
							{	
								$reference = $donnees->ref;
							}

							if(strpos($reference, $donnees->ref_client) !== false)
							{	
								$reference = $donnees->ref;
							}
						}
					}

					$sql = "SELECT fc.rowid as fc_id, fc.fk_soc as client, fc.ref as ref,  fc.total_ttc as amount, fc.datef, fc.paye as status";
					$sql.= " FROM ".MAIN_DB_PREFIX."facture as fc";
					$sql.= " WHERE fc.ref LIKE '%".$reference."%'";
					$sql.= " AND fc.fk_soc = ".$client_id;
					$sql.= " AND fc.total_ttc = ".$amount;
					$sql.= " AND fc.entity = ".$conf->entity;
					$sql.= " AND fc.paye = '1' ";
					$sql.= " AND fc.fk_statut = '2' ";

					$resql = $this->db->query($sql);

					if ($resql)
					{
						$num = $this->db->num_rows($resql);

						if ($num > 1)
						{
							$this->error = $langs->trans('Fetch found several records. Rename one of thirdparties to avoid duplicate.');
							dol_syslog($this->error, LOG_ERR);
							$result = -2;
						}
						elseif ($num == '1')   // $num = 1
						{
							$obj = $this->db->fetch_object($resql);

							$this->releve_facid     = $obj->fc_id;
							$this->releve_total_ttc = $obj->amount;
							$this->rappro           = '1'; // Lorque $num = 1 on cherche à rapprocher via les données de la facture.
							return 1;

						}
						elseif($num == '0')
						{
							$this->rappro = '2'; // Lorque $num = 0 on cherche à rapprocher via la référence de transaction.
							return 1;
						}
						else
						{	
							$this->fc_ok = '1';
							return 0;
						}
					}
				}
			}
		}

		public function create_paiement_client($ref, $settled_at, $emitted_at, $amount, $note, $num_paiement, $accountid, $facid, $paiementid, $type_paiement, $amount_value, $client_id, $thirdparty_name)
		{
			global $conf, $langs, $object, $user;

			$date_valeur = date_create($settled_at);
			$date_operation = date_create($emitted_at);
			$datev = date_format($date_valeur,"Y-m-d");
			$dateo = date_format($date_operation,"Y-m-d");
			$releve_annee = date_format($date_valeur,"Y");
			$releve_mois = date_format($date_valeur,"m");

			$now=dol_now();

			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'bank (';
			$sql.= 'datec, tms, datev, dateo, amount, label, fk_account, fk_user_author, num_chq, num_releve, fk_user_rappro, rappro, fk_type)';
			$sql.= " VALUES ('".$this->db->idate($now)."'";
			$sql.= ", '".$this->db->idate($now)."'";
			$sql.= ", '".$datev."'";
			$sql.= ", '".$dateo."'";
			$sql.= ", '".$amount_value."'";
			$sql.= ", '(CustomerInvoicePayment)'";
			$sql.= ", '".$accountid."'";
			$sql.= ", ".$user->id;
			$sql.= ", '".$num_paiement."'";
			$sql.= ", '".$releve_annee."".$releve_mois."'";
			$sql.= ", ".$user->id;
			$sql.= ", '1'";
			$sql.= ", '".$type_paiement."')";

			if ($test_client =='1') 
			{
				print $sql.'<br>';

			}else{
				
				$resql = $this->db->query($sql);
			}

			$bank_line_id = $this->db->last_insert_id(MAIN_DB_PREFIX.'bank');

			if ($resql || $test_client =='1') {

				$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'paiement (';
				$sql.= 'ref, entity, datec, datep, amount, multicurrency_amount, fk_paiement, num_paiement, note, fk_user_creat, fk_bank)';
				$sql.= " VALUES ('".$ref."', ".$conf->entity.", '".$this->db->idate($now)."',";
				$sql.= " '".$this->db->idate($datev)."', '".$amount."', '".$amount."', ".$paiementid.", '".$this->db->escape($num_paiement)."', '".$this->db->escape($note)."', ".$user->id.", ".$bank_line_id.")";

				if ($test_client =='1') 
				{
					print $sql.'<br>';

				}else{
					
					$resql = $this->db->query($sql);
				}

				if ($resql || $test_client =='1') {

					$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX.'paiement');
					
					$url = "/compta/paiement/card.php?id=";
					$label = '(paiement)';
					$type = "payment";

					$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'bank_url (';
					$sql.= 'fk_bank, url_id, url, label, type)';
					$sql.= " VALUES ('".$bank_line_id."'";
					$sql.= ", '".$this->id."'";
					$sql.= ", '".$url."'";
					$sql.= ", '".$label."'";
					$sql.= ", '".$type."')";

					if ($test_client =='1') 
					{
						print $sql.'<br>';

					}else{
						
						$resql = $this->db->query($sql);
					}

					if ($resql || $test_client == "1") {

						$url = "/comm/card.php?socid=";
						$label = '(paiement)';
						$type = "company";

						$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'bank_url (';
						$sql.= 'fk_bank, url_id, url, label, type)';
						$sql.= " VALUES ('".$bank_line_id."'";
						$sql.= ", '".$client_id."'";
						$sql.= ", '".$url."'";
						$sql.= ", '".$thirdparty_name."'";
						$sql.= ", '".$type."')";

						if ($test_client =='1') 
						{
							print $sql.'<br>';

						}else{

							$resql = $this->db->query($sql);
						}
					
						if ($resql || $test_client == "1") {

							if (is_numeric($amount) && $amount <> 0)
							{
								$amount = price2num($amount);
								$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'paiement_facture (fk_facture, fk_paiement, amount, multicurrency_amount)';
								$sql .= ' VALUES ('.$facid.','. $this->id.',\''.$amount.'\', \''.$amount.'\')';

								if ($test_client =='1') 
								{
									print $sql.'<br>';

								}else{
									
									$resql = $this->db->query($sql);
								}

								if ($resql || $test_client == "1") {

									$sql = "UPDATE ".MAIN_DB_PREFIX."facture SET paye = 1, fk_statut = 2 WHERE rowid = ".$facid;

									if ($test_client =='1') 
									{
										print $sql.'<br><br>';

									}else{

										$resql = $this->db->query($sql);
									}
									
									if ($resql) {
										$this->db->commit();

										$ref='0'; 
										$settled_at='0'; 
										$emitted_at='0'; 
										$amount='0'; 
										$note='0'; 
										$num_paiement='0'; 
										$accountid='0'; 
										$facid='0'; 
										$paiementid='0'; 
										$type_paiement='0'; 
										$amount_value='0'; 
										$fourn_id='0'; 
										$thirdparty_name='0'; 
										$this->result_create_client = '1';
										$this->result_rappro == '1';
										return 1 ;
									}else{
										$this->error = $langs->trans('Qonto_commit_error');
										dol_syslog($this->error, LOG_ERR);
										$result = -2;
									}
								}else{
									$this->error = $langs->trans('Qonto_set_invoice_to_payed_error');
									dol_syslog($this->error, LOG_ERR);
									$result = -2;
								}
							}
						}else{
							$this->error = $langs->trans('Qonto_Cannot_insert_link_between_facture_and_paiement');
							dol_syslog($this->error, LOG_ERR);
							$result = -2;
						}
					}else{
						$this->error = $langs->trans('Qonto_Cannot_insert_bank_url');
						dol_syslog($this->error, LOG_ERR);
						$result = -2;
					}
				}else{
					$this->error = $langs->trans('Qonto_Cannot_insert_payment_into_payment_table');
					dol_syslog($this->error, LOG_ERR);
					$result = -2;
				}
			}else{
				$this->error = $langs->trans('Qonto_Cannot_insert_bank_entry_into_bank_table');
				dol_syslog($this->error, LOG_ERR);
				$result = -2;
			}

		}

		public function QontoRapproWithTransaction($transaction_ref, $side, $amount, $fc_ok, $ff_ok, $settled_at, $emitted_at)
		{
			global $conf, $langs, $object, $user;

			$date_valeur = date_create($settled_at);
			$date_operation = date_create($emitted_at);
			$datev = date_format($date_valeur,"Y-m-d");
			$dateo = date_format($date_operation,"Y-m-d");
			$releve_annee = date_format($date_valeur,"Y");
			$releve_mois = date_format($date_valeur,"m");

			if( $fc_ok == '1' && $side == "credit"){
				$amount_value = $amount;
				$label = '(CustomerInvoicePayment)';

			}elseif ($ff_ok == '1' && $side == "debit") {
				$amount_value = "-".$amount;
				$label = '(SupplierInvoicePayment)';
			}

			$sql = 'SELECT rowid as fk_bank, num_chq, label FROM '.MAIN_DB_PREFIX.'bank';
			$sql.= ' WHERE';
			$sql.= ' num_chq LIKE "%'.$transaction_ref.'%"';
			$sql.= ' OR ( MONTH(datev) = "'.$releve_mois.'"';
			$sql.= ' AND YEAR(datev) = "'.$releve_annee.'"'; 
			$sql.= ' AND label = "'.$label.'"';
			$sql.= ' AND amount = "'.$amount_value.'")';

			//var_dump($sql);

			$resql = $this->db->query($sql);

			$num = $this->db->num_rows($resql);

			if ($num) {

				$obj = $this->db->fetch_object($resql);

				$fk_bank = $obj->fk_bank ;

				$sql = "UPDATE ".MAIN_DB_PREFIX."bank SET num_releve = '".$releve_annee."".$releve_mois."', fk_user_rappro = '".$user->id."', rappro = '1', datev = '".$datev."' WHERE rowid = '".$fk_bank."' AND rappro = '0'";

				if ($test_client =='1') 
				{

				}else{
					$resql = $this->db->query($sql);
					$nombre = $this->db->affected_rows($resql); 
				}

				if ($nombre) {

					$this->db->commit();
					$this->result_rappro = '1';
					return 1;

				}else{
					$this->error = $langs->trans('Cannot commit all function in the database');
					dol_syslog($this->error, LOG_ERR);
					$result = -2;
				}
			}
		}

		public function QontoRapproWithFacid($fc_ok, $ff_ok, $side, $amount, $releve_facid, $releve_total_ttc, $settled_at, $emitted_at)
		{

			global $conf, $langs, $object, $user;

			$date_valeur = date_create($settled_at);
			$date_operation = date_create($emitted_at);
			$datev = date_format($date_valeur,"Y-m-d");
			$dateo = date_format($date_operation,"Y-m-d");
			$releve_annee = date_format($date_valeur,"Y");
			$releve_mois = date_format($date_valeur,"m");


			if( $fc_ok == '1' && $side == "credit"){
				$amount_value = $amount;
				$label = '(CustomerInvoicePayment)';
				$type = 'payment';

			}elseif ($ff_ok == '1' && $side == "debit") {
				$amount_value = "-".$amount;
				$label = '(SupplierInvoicePayment)';
				$type = 'payment_supplier';
			}

			if ($ff_ok == '1') {
				$sql = 'SELECT fk_facturefourn, fk_paiementfourn as fk_paiement, amount, multicurrency_amount FROM '.MAIN_DB_PREFIX.'paiementfourn_facturefourn';
				$sql.= ' WHERE';
				$sql.= ' fk_facturefourn = '.$releve_facid;
				$sql.= ' AND amount = '.$releve_total_ttc;
			}

			if ($fc_ok == '1') {
				$sql = 'SELECT fk_facture, fk_paiement as fk_paiement, amount, multicurrency_amount FROM '.MAIN_DB_PREFIX.'paiement_facture';
				$sql.= ' WHERE';
				$sql.= ' fk_facture = '.$releve_facid;
				$sql.= ' AND amount = '.$releve_total_ttc;
			}
			$resql = $this->db->query($sql);

			$num = $this->db->num_rows($resql);

			if ($num) {

				$obj = $this->db->fetch_object($resql);

				$releve_fk_paiement = $obj->fk_paiement;
				$releve_amount = $obj->amount;

				$sql = 'SELECT fk_bank, url_id, type FROM '.MAIN_DB_PREFIX.'bank_url';
				$sql.= ' WHERE';
				$sql.= ' url_id = '.$releve_fk_paiement;
				$sql.= ' AND type = "'.$type.'"';

				$resql = $this->db->query($sql);

				$num = $this->db->num_rows($resql);

				if ($num) {

					$obj = $this->db->fetch_object($resql);

					$releve_fk_bank = $obj->fk_bank ;

					$sql = "UPDATE ".MAIN_DB_PREFIX."bank SET num_releve = '".$releve_annee."".$releve_mois."', fk_user_rappro = '".$user->id."', rappro = '1' WHERE rowid = '".$releve_fk_bank."' AND rappro = '0'";

					if ($printsql =='1') 
					{
						print $sql.'<br><br>';

					}else{

						$resql = $this->db->query($sql);
						$nombre = $this->db->affected_rows($resql); 
					}
					
					if ($nombre) {
						
						$this->db->commit();
						$this->result_rappro = '1';	
						return 1;

					}else{
						$this->error = $langs->trans('Cannot commit all function in the database');
						dol_syslog($this->error, LOG_ERR);
						$result = -2;
					}
				}
			}
		}

//-------		
	}
?>
