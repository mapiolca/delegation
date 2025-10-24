<?php $text = '

<p>&nbsp;<strong>CHANTIER : '.$outputlangs->convToOutputCharset($object->array_options['options_ref_ch_client']).' &laquo; '.$outputlangs->convToOutputCharset($object->array_options['options_lib_ch_client']).' &raquo;</strong><br/><br/></p>

<table style="border:1px; solid; black; position:absolute; top:201px; left:149px; white-space:nowrap">
    <tr>
        <td style="vertical-align:middle; font-size:14x;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CONVENTION DE DELEGATION DE PAIEMENT POUR LE MARCHE<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PRINCIPAL DU LOT '.$outputlangs->convToOutputCharset($object->array_options['options_lib_lot']).'
        </td>
    </tr>
</table>
<br/><br/><br/><br/>
<p>Entre les soussign&eacute;s :<br/><br/></p>



<p>Soci&eacute;t&eacute; :&nbsp;&nbsp;&nbsp;&nbsp;<strong>&nbsp;&nbsp;&nbsp;&nbsp;<strong> '.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).'<br/>&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client_adresse']).'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client_cs']).' '.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client_codepostale']).' '.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client_ville']).'</strong></p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entreprise principale ou Maître d\'Ouvrage<br/><br/></p>

<p>Soci&eacute;t&eacute; : &nbsp; &nbsp; &nbsp; &nbsp; <strong>INPOSE</strong></p>

<p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;14b Chemin Lou Tribail<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;33610 CESTAS</strong></p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entreprise sous-traitante<br/><br/></p>

<p>Soci&eacute;t&eacute; : &nbsp; &nbsp; &nbsp; &nbsp; <strong>'.$outputlangs->convToOutputCharset($object->thirdparty->name).'</strong></p>

<p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$outputlangs->convToOutputCharset($object->thirdparty->address).'</strong><br/><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<strong>'.$outputlangs->convToOutputCharset($object->thirdparty->zip).' '.$outputlangs->convToOutputCharset($object->thirdparty->town).'</strong></p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fournisseur</p><br/><br/>

<p><strong>Il est pr&eacute;alablement expos&eacute; :</strong></p><br/><br/>

<p>Dans le cadre du chantier '.$outputlangs->convToOutputCharset($object->array_options['options_lib_ch_client']).' situ&eacute; '.$outputlangs->convToOutputCharset($object->array_options['options_ad_ch_client']).', la soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).' </strong>a confi&eacute; en sous-traitance &agrave; la soci&eacute;t&eacute; <strong>INPOSE </strong>les travaux relatifs <strong>&agrave; <em>'.$outputlangs->convToOutputCharset($object->array_options['options_trav']).' </em></strong>pour un montant global et forfaitaire de <strong>'.price($outputlangs->convToOutputCharset($object->array_options['options_amount'])).' &euro; HT.</strong></p>

<p>A la demande du sous-traitant, <strong>'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).' &nbsp; </strong>a accept&eacute;, afin de faciliter la tr&eacute;sorerie du sous-traitant et&nbsp; d&rsquo;assurer un approvisionnement correct du chantier, de r&eacute;gler directement &agrave; la soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->thirdparty->name).' </strong>la fourniture d\'éléments relatifs au poste <strong><em>'.$outputlangs->convToOutputCharset($object->array_options['options_poste_cctp']).'</em></strong> dans le cadre d&rsquo;une d&eacute;l&eacute;gation de paiement conform&eacute;ment aux dispositions de l&rsquo;article 1275 du code civil.</p>';

$text2 ='<p><br/><br/><br/><strong>Il est convenu ce qui suit :</strong></p>

<p style="text-decoration: underline">Article 1 &ndash; Commande des mat&eacute;riaux et fournitures</p>

<p>Les mat&eacute;riaux objet de la pr&eacute;sente d&eacute;l&eacute;gation sont &eacute;num&eacute;r&eacute;s, d&eacute;finis et quantifi&eacute;s de mani&egrave;re strictement limitative dans le document annex&eacute; &agrave; la pr&eacute;sente (Devis '.$outputlangs->convToOutputCharset($object->thirdparty->name).' '.$outputlangs->convToOutputCharset($object->array_options['options_n_devis_fourn']).' du '.$outputlangs->convToOutputCharset($object->array_options['options_date_devis_fourn']).' et Commande INPOSE '.$outputlangs->convToOutputCharset($object->ref).')'.$outputlangs->convToOutputCharset($object->date_commande).'.</p>

<p>Les caract&eacute;ristiques des mat&eacute;riaux et leurs quantit&eacute;s ont &eacute;t&eacute; &eacute;tablies par le sous-traitant (la soci&eacute;t&eacute; <strong>INPOSE</strong>) sous son enti&egrave;re responsabilit&eacute; pour r&eacute;pondre aux sp&eacute;cifications de son contrat.</p>

<p>Le montant maximum de la d&eacute;l&eacute;gation est arr&ecirc;t&eacute; &agrave; la somme de <strong>'.price($object->total_ht).' &euro; HT</strong>.</p>

<p>Toute nouvelle commande devra faire l&rsquo;objet d&rsquo;une nouvelle d&eacute;l&eacute;gation de paiement</p>

<p style="text-decoration: underline">Article 2 &ndash; Livraison</p>

<p>Chaque livraison devra &ecirc;tre accompagn&eacute;e d&rsquo;un bon de livraison qui sera v&eacute;rifi&eacute; et sign&eacute; par le repr&eacute;sentant de la soci&eacute;t&eacute; <strong>INPOSE</strong>. Un double sign&eacute; sera remis au repr&eacute;sentant de la soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).' </strong>sur le chantier. La soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).' </strong>se r&eacute;serve le droit de v&eacute;rifier &agrave; tout moment l&rsquo;&eacute;tat des livraisons et du stock sur le chantier.</p>

<p style="text-decoration: underline">Article 3 &ndash; Facturation</p>

<p>Les factures relatives &agrave; ces mat&eacute;riaux seront &eacute;tablies au nom de la soci&eacute;t&eacute; <strong>INPOSE</strong>.</p>

<p>Un double de chaque facture devra &ecirc;tre adress&eacute; par la soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->thirdparty->name).' </strong>&agrave; la Soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).' &nbsp; </strong>en recommand&eacute; avec accus&eacute; de r&eacute;ception, d&egrave;s l&rsquo;&eacute;tablissement de la facture et au plus tard le 10 du mois suivant la livraison. Tout manquement &agrave; cette obligation d&eacute;chargera la soci&eacute;t&eacute; <strong> </strong>de son obligation de paiement.</p>

<p style="text-decoration: underline">Article 4 &ndash; Paiements</p>

<p>La&nbsp; soci&eacute;t&eacute;&nbsp; <strong>INPOSE&nbsp; </strong>devra&nbsp; remettre&nbsp; &agrave;&nbsp; la&nbsp; soci&eacute;t&eacute;&nbsp; <strong>'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).' </strong>une copie accept&eacute;e de chaque facture dans les cinq jours suivant la r&eacute;ception de ladite facture. Les factures devront comporter l&rsquo;acceptation du sous traitant par la mention</p>

<p>&laquo; Bon pour paiement pour bon compte &raquo; suivie de la signature du g&eacute;rant et du tampon de la soci&eacute;t&eacute; <strong>INPOSE</strong>. Les factures seront accept&eacute;es, par le sous-traitant ; et seront r&eacute;gl&eacute;es en ses lieux et place par l&rsquo;entreprise principale par virement &agrave; 60 jours date de facture ou &agrave; 45 jours fin de mois suivant. Les paiements au fournisseur viendront de plein droit en d&eacute;duction des sommes dues &agrave; la soci&eacute;t&eacute; <strong>INPOSE</strong>. Un avenant au contrat de sous-traitance initial de la soci&eacute;t&eacute; <strong>INPOSE </strong>r&eacute;gularisera cette disposition.</p>

<p style="text-decoration: underline">Article 5 &ndash; Propri&eacute;t&eacute;</p>

<p>La soci&eacute;t&eacute; <strong>INPOSE </strong>conservera l&rsquo;enti&egrave;re responsabilit&eacute; de la garde et de la conservation en bon &eacute;tat des mat&eacute;riaux jusqu&rsquo;&agrave; la r&eacute;ception des ouvrages. En cas de perte, de vol ou de d&eacute;t&eacute;rioration, les mat&eacute;riaux seront remplac&eacute;s aux frais de la soci&eacute;t&eacute; <strong>INPOSE </strong>qui fera sonaffaire personnelle de tout recours contre les auteurs ou aupr&egrave;s de sa compagnie d&rsquo;assurance et garantit la soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).' </strong>de toutes les cons&eacute;quences de ces recours.</p>

<p style="text-decoration: underline">Article 6 &ndash; Responsabilit&eacute;s</p>

<p>Les dispositions qui pr&eacute;c&egrave;dent n&rsquo;ont pas pour effet de d&eacute;charger la soci&eacute;t&eacute; <strong>INPOSE </strong>des responsabilit&eacute;s qui lui incombent tant en ce qui concerne la qualit&eacute; et la conformit&eacute; des mat&eacute;riaux que leur mise en &oelig;uvre. La soci&eacute;t&eacute; <strong>INPOSE </strong>devra en outre faire son affaire personnelle de tout recours et r&eacute;clamations contre les fournisseurs, transporteurs et autres intervenants en cas de non-conformit&eacute;, d&eacute;t&eacute;riorations ou vices et proc&eacute;dera imm&eacute;diatement au remplacement &agrave; ses frais.<br/><br/><br/><br/><br/></p>

<p style="text-decoration: underline">Article 7 &ndash; Acceptation de la d&eacute;l&eacute;gation</p>



<p>La soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->thirdparty->name).' </strong>accepte la d&eacute;l&eacute;gation de paiement &eacute;tablie entre la soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).' </strong>et la soci&eacute;t&eacute; <strong>INPOSE </strong>et le paiement par la soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).' </strong>des sommes lui étant dues par la soci&eacute;t&eacute; <strong>INPOSE </strong>pour le montant et les modalit&eacute;s vis&eacute;es ci-dessus.</p>

<p>La soci&eacute;t&eacute; <strong>'.$outputlangs->convToOutputCharset($object->thirdparty->name).' </strong>reconna&icirc;t&nbsp; que&nbsp; la&nbsp; d&eacute;l&eacute;gation&nbsp; objet&nbsp; des&nbsp; pr&eacute;sentes ne&nbsp; concerne que&nbsp; les mat&eacute;riaux limitativement &eacute;nonc&eacute;s dans l&rsquo;annexe et ne peut, en aucun cas, s&rsquo;appliquer &agrave; des commandes pass&eacute;es ou &agrave; venir de la soci&eacute;t&eacute; <strong>INPOSE.</strong></p>

<p style="text-decoration: underline">Article 8 &ndash; R&eacute;siliation</p>

<p>La pr&eacute;sente d&eacute;l&eacute;gation pourra &ecirc;tre r&eacute;sili&eacute;e par l&rsquo;une ou l&rsquo;autre partie par lettre recommand&eacute;e avec accus&eacute; de r&eacute;ception, la date d&rsquo;effet de la r&eacute;siliation &eacute;tant le lendemain de la r&eacute;ception du courrier.</p>

<p>Les dispositions de la pr&eacute;sente convention continuent &agrave; s&rsquo;appliquer &agrave; toutes les livraisons de mat&eacute;riaux effectu&eacute;es jusqu&rsquo;&agrave; la date de r&eacute;siliation.</p>

<p style="text-decoration: underline">Article 9 &ndash; Election de domicile &ndash; Litiges</p>

<p>Les parties font &eacute;lection de domicile &agrave; leur adresse vis&eacute;e en t&ecirc;te des pr&eacute;sentes.</p>

<p>Les&nbsp; litiges&nbsp; qui&nbsp; pourraient s&rsquo;&eacute;lever &agrave;&nbsp; l&rsquo;occasion de l&rsquo;ex&eacute;cution ou de&nbsp; l&rsquo;interpr&eacute;tation des pr&eacute;sentes seront soumis aux tribunaux de BORDEAUX auxquels les parties font attribution de juridiction.<br/><br/><br/><br/></p>

<p><br/><br/><br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fait &agrave; Bordeaux, le &hellip;&hellip;&hellip;&hellip;. En 3 exemplaires.<p>';

$text3 ='

<table>
    <tr>
        <td>
            <p>
                <strong>
                    '.$outputlangs->convToOutputCharset($object->array_options['options_deleg_client']).'<br/>
                </strong>
                Tampon Signature
            </p>
        </td>
        <td>
            <p>

                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <strong>
                    '.$outputlangs->convToOutputCharset($object->thirdparty->name).'<br/>
                </strong>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tampon Signature
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p>
                &laquo; Bon pour d&eacute;l&eacute;gation de paiement
            </p>
        </td>
        <td>
            <p>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &laquo; Bon pour d&eacute;l&eacute;gation de paiement
            </p>
        </td>
    </tr>
    <tr style="vertical-align">
        <td colspan="2">
            <p>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <strong>INPOSE<br/></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tampon Signature
            </p>
            <p>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                &laquo; Lu et approuv&eacute; &raquo;
            </p>
        </td>
    </tr>
</table>';

?>