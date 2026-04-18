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

llxHeader('', $langs->trans('Delegation'));

if ($formconfirm) {
	print $formconfirm;
}

if ($message) {
	print dol_htmloutput_mesg($message, '', ($error ? 'error' : 'ok'), 0);
}

print dol_get_fiche_head($head, $current_head, $langs->trans('InvoiceCustomer'), -1, 'bill');

$socid = 0;
if (! empty($soc) && ! empty($soc->id)) {
	$socid = $soc->id;
} elseif (! empty($object->thirdparty) && ! empty($object->thirdparty->id)) {
	$socid = $object->thirdparty->id;
}
$linkback = '<a href="'.DOL_URL_ROOT.'/compta/facture/list.php?restore_lastsearch_values=1'.($socid ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';

$morehtmlref = '<div class="refidno">';
$morehtmlref .= $form->editfieldkey("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', 0, 1);
$morehtmlref .= $form->editfieldval("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', null, null, '', 1);
if (! empty($soc) && ! empty($soc->id)) {
	$morehtmlref .= '<br>'.$langs->trans('ThirdParty').' : '.$soc->getNomUrl(1, 'compta');
} elseif (! empty($object->thirdparty) && ! empty($object->thirdparty->id)) {
	$morehtmlref .= '<br>'.$langs->trans('ThirdParty').' : '.$object->thirdparty->getNomUrl(1, 'compta');
}
if (! empty($conf->projet->enabled) && ! empty($project) && ! empty($project->id)) {
	$morehtmlref .= '<br>'.$langs->trans('Project').' : '.$project->getNomUrl(1);
}
$morehtmlref .= '</div>';

dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

print '<div class="fichecenter">';
print '<div class="underbanner clearboth"></div>';

?>

<table class="border centpercent tableforfield">
	<tr>
		<td class="titlefield"><?php echo $langs->trans('Ref'); ?></td>
		<td><?php echo $object->ref; ?></td>
	</tr>

	<tr>
		<td class="titlefield"><?php echo $langs->trans('RefCustomer'); ?></td>
		<td><?php echo $object->ref_client; ?></td>
	</tr>

	<tr>
		<td class="titlefield"><?php echo $langs->trans('Company'); ?></td>
		<td><?php echo $soc ? $soc->getNomUrl(1, 'compta') : ''; ?></td>
	</tr>

	<?php if (! empty($conf->projet->enabled)) { ?>
	<tr>
		<td class="titlefield"><?php echo $langs->trans('Project'); ?></td>
		<td><?php echo (! empty($project) && ! empty($project->id)) ? $project->getNomUrl(1) : ''; ?></td>
	</tr>
	<?php } ?>
</table>
<br>

<div class="div-table-responsive">
<table id="tablelines" class="noborder centpercent">
<?php if ($numLines > 0){ ?>
	<tr class="liste_titre nodrag nodrop">
		<td><?php echo $langs->trans('Supplier'); ?></td>
		<td><?php echo $langs->trans('Ref'); ?></td>
		<td><?php echo $langs->trans('Date'); ?></td>
		<td><?php echo $langs->trans('AmountHT'); ?></td>
		<td><?php echo $langs->trans('VAT'); ?></td>
		<td><?php echo $langs->trans('AmountTTC'); ?></td>
		<td width="50">&nbsp;</td>
	</tr>

<?php

for ($i = 0; $i < $numLines; $i++) {
	$line = $delegation->lines[$i];
	?>

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
		<td><?php echo ! empty($line->supplier_invoice) ? price($line->supplier_invoice->total_ht) : '&nbsp;'; ?></td>
		<td><?php echo ! empty($line->supplier_invoice) ? price($line->supplier_invoice->total_tva) : '&nbsp;'; ?></td>
		<td><?php echo ! empty($line->supplier_invoice) ? price($line->supplier_invoice->total_ttc) : '&nbsp;'; ?></td>
		<td align="right">
			<?php if ($canDeleteLines) { ?>
				<a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deleteline&amp;lineid='.$line->rowid.'&amp;token='.newToken(); ?>">
					<?php echo img_delete(); ?>
				</a>
			<?php } ?>
		</td>
	</tr>
<?php
}
?>


<?php } ?>


<?php if ($canAddLines){ ?>

<tr class="liste_titre nodrag nodrop">
	<td colspan="7"><?php echo $form->textwithpicto($langs->trans("DelegationSelectSupplierInvoice"), $langs->trans("DelegationSelectSupplierInvoiceHelp")); ?></td>
</tr>

<form action="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id; ?>" method="POST">
<input type="hidden" name="token" value="<?php echo newToken(); ?>" />
<input type="hidden" name="action" value="addsupplierinvoice" />
<input type="hidden" name="id" value="<?php echo $object->id; ?>" />

		<tr class="pair">
			<td colspan="6">
			<?php
			if (! empty($supplierInvoiceOptions)) {
				echo $form->selectarray('fk_facture_fourn', $supplierInvoiceOptions, '', 1);
			} else {
				echo $langs->trans('DelegationSupplierInvoices');
			}
			?>
		</td>
		<td align="right">
			<?php if (! empty($supplierInvoiceOptions)) { ?>
			<input type="submit" class="button" value="<?php echo $langs->trans("Add"); ?>" name="addline" />
			<?php } ?>
		</td>
	</tr>

</form>
<?php } ?>
</table>
</div>

</div>

<?php print dol_get_fiche_end(); ?>

<?php llxFooter(); ?>
