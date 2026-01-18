<?php

/* Copyright (C) 2012           Mikael Carlavan        <contact@mika-carl.fr>
 *                                                              http://www.mikael-carlavan.fr
 * Copyright (C) 2012-2020      Pierre Ardoin          <mapiolca@me.com>
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**	    \file       htdocs/delegation/tpl/delegation.default.tpl.php
 *		\ingroup    delegation
 *		\brief      Delegation module default view
 */

llxHeader();

echo ($message ? dol_htmloutput_mesg($message, '', ($error ? 'error' : 'ok'), 0) : '');

dol_fiche_head($head, $current_head, $langs->trans('Delegation'));


?>

<?php echo $formconfirm ? $formconfirm : ''; ?>

<table class="border" width="100%">
    <tr>
        <td width="20%"><?php echo $langs->trans('Ref'); ?></td>
        <td><?php echo $object->ref; ?></td>
    </tr>

    <tr>
        <td><?php echo $langs->trans('RefCustomer'); ?></td>
        <td><?php echo $object->ref_client; ?></td>
    </tr>

    <tr>
        <td><?php echo $langs->trans('Company'); ?></td>
        <td><?php echo $soc->getNomUrl(1,'compta'); ?></td>
    </tr>

    <tr>
        <td><?php echo $langs->trans('Type'); ?></td>
        <td><?php echo $object->getLibType(); ?></td>
    </tr>

    <tr>
        <td><?php echo $langs->trans('Date'); ?></td>
        <td><?php echo dol_print_date($object->date,'daytext'); ?></td>
    </tr>    
  

    <tr>
        <td><?php echo $langs->trans('AmountHT'); ?></td>
        <td><?php echo price($object->total_ht,1,'',1,-1,-1,$conf->currency); ?></td>
    </tr>

     <tr>
        <td><?php echo $langs->trans('AmountVAT'); ?></td>
        <td><?php echo price($object->total_tva,1,'',1,-1,-1,$conf->currency); ?></td>
    </tr>

    <tr>
        <td><?php echo $langs->trans('AmountTTC'); ?></td>
        <td><?php echo price($object->total_ttc,1,'',1,-1,-1,$conf->currency); ?></td>
    </tr>

     <tr>
        <td><?php echo $langs->trans('Status'); ?></td>
        <td align="left" colspan="3"><?php echo $object->getLibStatut(4, $totalpaye); ?></td>
    </tr>

    <?php if ($conf->projet->enabled){ ?>
      <tr>
        <td><?php echo $langs->trans('Project'); ?></td>
        <td><?php if ($project->id > 0){ echo $project->getNomUrl(1); } ?></td>
    </tr>
   <?php } ?>
</table>
<br />

<table id="tablelines" class="noborder" width="100%">
<?php if ($numLines > 0){ ?>
	<tr class="liste_titre nodrag nodrop">
		<td><?php echo $langs->trans('Supplier'); ?></td>
		<td><?php echo $langs->trans('Ref'); ?></td>
		<td><?php echo $langs->trans('Date'); ?></td>
		<td><?php echo $langs->trans('AmountTTC'); ?></td>
		<td><?php echo $langs->trans('AlreadyPaid'); ?></td>
		<td><?php echo $langs->trans('RemainToPay'); ?></td>
		<td><?php echo $langs->trans('Amount'); ?></td>
		<td width="50">&nbsp;</td>
	</tr>

<?php

for ($i = 0; $i < $numLines; $i++) {
	$line = $delegation->lines[$i];

	if ($action == 'editline' && $lineid == $line->rowid){ ?>

	<form action="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id; ?>" method="POST">
	<input type="hidden" name="token" value="<?php  echo $_SESSION['newtoken']; ?>" />
	<input type="hidden" name="action" value="updateline" />
	<input type="hidden" name="id" value="<?php echo $object->id; ?>" />
	<input type="hidden" name="lineid" value="<?php echo $line->rowid; ?>" />

	<?php } ?>

	<tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
		<td>
			<?php
			if (! empty($line->supplier_invoice)) {
				echo $line->supplier_invoice->thirdparty->getNomUrl(1);
			} else {
				echo '&nbsp;';
			}
			?>
		</td>
		<td>
			<?php
			if (! empty($line->supplier_invoice)) {
				echo $line->supplier_invoice->getNomUrl(1);
			} else {
				echo $line->label;
			}
			?>
		</td>
		<td>
			<?php
			if (! empty($line->supplier_invoice)) {
				$dateInvoice = ! empty($line->supplier_invoice->datef) ? $line->supplier_invoice->datef : $line->supplier_invoice->date;
				echo dol_print_date($dateInvoice, 'daytext');
			} else {
				echo '&nbsp;';
			}
			?>
		</td>
		<td><?php echo ! empty($line->supplier_invoice) ? price($line->supplier_invoice->total_ttc) : '&nbsp;'; ?></td>
		<td><?php echo ! empty($line->supplier_invoice) ? price($line->supplier_invoice_paid) : '&nbsp;'; ?></td>
		<td><?php echo ! empty($line->supplier_invoice) ? price($line->supplier_invoice_remaining) : '&nbsp;'; ?></td>
		<td>
			<?php if ($action == 'editline' && $lineid == $line->rowid){ ?>
				<input type="text" size="8" id="amount" name="amount" value="<?php echo price($line->amount); ?>" />
				<input type="hidden" id="label" name="label" value="<?php echo dol_escape_htmltag($line->label); ?>" />
			<?php }else{
				echo price($line->amount);
			} ?>
		</td>

		<?php if ($action == 'editline' && $lineid == $line->rowid){ ?>
		<td align="right">
			<input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
		</td>

		<?php }else{ ?>
			<td align="right">
			<?php if ($canAddLines) { ?>
				<a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid; ?>">
					<?php echo img_edit(); ?>
				</a>
			<?php } ?>
			<?php if ($canDeleteLines) { ?>
				<a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deleteline&amp;lineid='.$line->rowid; ?>">
					<?php echo img_delete(); ?>
				</a>
			<?php } ?>
			</td>
		<?php } ?>
	</tr>
	</form>
<?php } ?>


<?php } ?>


<?php if ($canAddLines){ ?>

<tr class="liste_titre nodrag nodrop">
	<td colspan="8"><?php echo $langs->trans("DelegationSelectSupplierInvoice"); ?></td>
</tr>

<form action="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id; ?>" method="POST">
<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
<input type="hidden" name="action" value="addsupplierinvoice" />
<input type="hidden" name="id" value="<?php echo $object->id; ?>" />

<tr class="pair">
	<td colspan="7">
		<?php
		if (! empty($supplierInvoiceOptions)) {
			echo $form->selectarray('fk_facture_fourn', $supplierInvoiceOptions, '', 1);
		} else {
			echo $langs->trans('DelegationSupplierInvoices');
		}
		?>
	</td>
	<td align="right">
		<input type="submit" class="button" value="<?php echo $langs->trans("Add"); ?>" name="addline" />
	</td>
</tr>

</form>
<?php } ?>
</table>

</div>

<br />

<?php dol_fiche_end(); ?>

<?php llxFooter(''); ?>
