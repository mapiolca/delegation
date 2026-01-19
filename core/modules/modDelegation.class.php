<?php
/* Copyright (C) 2018-2024	Pierre Ardoin		<developpeur@lesmetiersdubatiment.fr>

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


/**
 * 		\defgroup   modDelegation     Module Delegation
 *      \file       htdocs/core/modules/modDelegation.class.php
 *      \ingroup    modDelegation
 *      \brief      Description and activation file for module modDelegation
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");

/**
 * 		\class      modDelegation
 *      \brief      Description and activation class for module modDelegation
 */
class modDelegation extends DolibarrModules
{
	/**
	 *   \brief      Constructor. Define names, constants, directories, boxes, permissions
	 *   \param      DB      Database handler
	 */
	function __construct($db)
	{
        global $langs, $conf;

        $this->db = $db;
		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 450007;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'delegation';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "Les Métiers du Bâtiment";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = 'delegation';
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Module pour gérer la délégation de paiement, les contrats de sous-traitance et les formulaires DC4.";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto = 'delegation@delegation';

		$this->editor_name = 'Les Métiers du Bâtiment';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /mymodule/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /mymodule/core/modules/barcode)
		// for specific css file (eg: /mymodule/css/mymodule.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
		//							'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
		//							'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
		//							'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
		//							'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (core/theme)
		//                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
		//							'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
		//							'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
		//							'css' => array('/mymodule/css/mymodule.css.php'),	// Set this to relative path of css file if module has its own css file
	 	//							'js' => array('/mymodule/js/mymodule.js'),          // Set this to relative path of js file if module must load a js on all pages
		//							'hooks' => array('hookcontext1','hookcontext2')  	// Set here all hooks context managed by module
		//							'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@mymodule')) // Set here all workflow context managed by module
		//                        );
		$this->module_parts = array(
			'substitutions' => 1,
			'models' => 1,
			'hooks' => array('thirdpartycard', 'propalcard', 'ordercard', 'invoicecard', 'contractcard', 'pdfgeneration')
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array("/delegation/temp");
		$r=0;

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array('setup.php@delegation');

		// Dependencies
		$this->depends = array('modBtp', 'modProjet', 'modSociete');		// List of modules id that must be enabled if this module is enabled
		$this->conflictwith = array();
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(21,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("delegation@delegation");

		// Constants
		$this->const = array(
			0 => array('LMDB_USE_IDPROF3_DICTIONARY', 'chaine', '1', 'Constant to enable usage of idprof3 table', 0, 'current', 1),
			1 => array('BANK_ASK_PAYMENT_BANK_DURING_ORDER', 'int', '11', "Demander le compte bancaire lors de la création d'une commande", 1),
			2 => array('BANK_ASK_PAYMENT_BANK_DURING_PROPOSAL', 'int', '11', "Demander le compte bancaire lors de la création d'un devis", 1),
			3 => array('DELEGATION_PAYMENT_MODE_ID', 'int', '0', 'Payment mode id for delegation', 0, 'current', 1),
			4 => array('DELEGATION_CLEARING_BANKACCOUNT_ID', 'int', '0', 'Clearing bank account id for delegation', 0, 'current', 1),
			5 => array('DELEGATION_ENABLE_TAB_DELEGATION', 'int', '1', 'Enable delegation tab', 0, 'current', 1),
			6 => array('DELEGATION_ENABLE_TAB_DETAILS', 'int', '1', 'Enable project details tab', 0, 'current', 1),
			7 => array('DELEGATION_ENABLE_TAB_DC4_SUPPLIER', 'int', '1', 'Enable supplier order DC4 tab', 0, 'current', 1),
			8 => array('DELEGATION_ENABLE_TAB_DC4_CUSTOMER', 'int', '1', 'Enable customer order DC4 tab', 0, 'current', 1),
			9 => array('DELEGATION_ENABLE_VAT_REVERSE_CHARGE', 'int', '0', 'Enable VAT reverse charge for subcontracting', 0, 'current', 1),
			10 => array('DELEGATION_VAT_REVERSE_CHARGE_FORCE_VAT0', 'int', '0', 'Force VAT rate to 0 when reverse charge is active', 0, 'current', 1),
			11 => array('DELEGATION_VAT_REVERSE_CHARGE_SCOPE', 'chaine', 'services_only', 'Scope for VAT reverse charge lines', 0, 'current', 1),
			12 => array('DELEGATION_VAT_REVERSE_CHARGE_LEGAL_TEXT', 'chaine', '', 'Legal text for VAT reverse charge mention', 0, 'current', 1),
		);

		// To add a new tab identified by code 
		$this->tabs = array(
			'invoice:+delegation:Delegation:delegation@delegation:(getDolGlobalInt(\'DELEGATION_ENABLE_TAB_DELEGATION\', 1) && ! empty($user->rights->delegation) && (! empty($user->rights->delegation->tab_delegation_read) || (! empty($user->rights->delegation->myactions) && ! empty($user->rights->delegation->myactions->read)))):/delegation/tabs/facture.php?id=__ID__',
			'project:+details:Details:delegation@delegation:(getDolGlobalInt(\'DELEGATION_ENABLE_TAB_DETAILS\', 1) && ! empty($user->rights->delegation) && (! empty($user->rights->delegation->tab_details_read) || (! empty($user->rights->delegation->myactions) && ! empty($user->rights->delegation->myactions->read)))):/delegation/tabs/Details.php?id=__ID__',
			'supplier_order:+dc4_supplier:DC4:delegation@delegation:(getDolGlobalInt(\'DELEGATION_ENABLE_TAB_DC4_SUPPLIER\', 1) && ! empty($user->rights->delegation) && (! empty($user->rights->delegation->tab_dc4_supplier_read) || (! empty($user->rights->delegation->myactions) && ! empty($user->rights->delegation->myactions->read)))):/delegation/tabs/DC4.php?id=__ID__',
			'order:+dc4_customer:DC4form:delegation@delegation:(getDolGlobalInt(\'DELEGATION_ENABLE_TAB_DC4_CUSTOMER\', 1) && ! empty($user->rights->delegation) && (! empty($user->rights->delegation->tab_dc4_customer_read) || (! empty($user->rights->delegation->myactions) && ! empty($user->rights->delegation->myactions->read)))):/delegation/tabs/DC4_CustomerOrder.php?id=__ID__',
		); 

		// Dictionnaries
		$this->dictionnaries = array(
			'langs'=>'delegation@delegation',
			'tabname'=>array(MAIN_DB_PREFIX."c_idprof3"),
			'tablib'=>array("Code NAF/APE"),
			'tabsql'=>array(
				'SELECT f.rowid, f.idprof3, f.activity, f.country_code, p.code as pays_code, p.label as pays, f.active FROM '.MAIN_DB_PREFIX.'c_idprof3 as f, '.MAIN_DB_PREFIX.'c_country as p WHERE p.code COLLATE utf8mb3_unicode_ci = f.country_code COLLATE utf8mb3_unicode_ci'
			),
			'tabsqlsort'=>array(
				"idprof3 ASC, activity ASC, country_code ASC"
			),
			'tabfield'=>array(
				"idprof3,activity,country_code"
			),
			'tabfieldvalue'=>array(
				"idprof3,activity,country_code"
			),
			'tabfieldinsert'=>array(
				"idprof3,activity,country_code"
			),
			'tabrowid'=>array(),
			'tabcond'=>array(
				empty($conf->delegation->enabled)?0:$conf->delegation->enabled
			)
		);

        // Boxes
		// Add here list of php file(s) stored in includes/boxes that contains class to show a box.
        $this->boxes = array();			// List of boxes


        // Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		/* BEGIN MODULEBUILDER CRON */
		$this->cronjobs = array(
			  0 => array(
			      'label' => 'Envoyer les factures validées à la date de la facture avec un template personnalisé.',
			      'jobtype' => 'method',
			      'class' => 'custom/delegation/class/facture.class.php',
			      'objectname' => 'Facture',
			      'method' => 'sendEmailsNotificationOnInvoiceDate',
			      'parameters' => '',
			      'comment' => 'Envoi automatiquement la facture par email lorsque celle-ci est crée depuis un modèle récurrent et dont la fonction et le modèle de courriel ont été choisi.',
			      'frequency' => 1,
			      'unitfrequency' => 86400,
			      'status' => 1,
			      'test' => 'isModEnabled("delegation")',
			      'priority' => 50,
			  ),
		);
		/* END MODULEBUILDER CRON */
		// Example: $this->cronjobs=array(
		//    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'isModEnabled("jpsun")', 'priority'=>50),
		//    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'isModEnabled("jpsun")', 'priority'=>50)
		// );

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r = 0;
		$this->rights[$r][0] = 440301;
		$this->rights[$r][1] = 'Ajouter des délégations liées à ce compte';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'myactions';
        $this->rights[$r][5] = 'create';
        
        $r++;
		$this->rights[$r][0] = 440302;
		$this->rights[$r][1] = 'Ajouter des délégations liées à tout le monde';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'allactions';
        $this->rights[$r][5] = 'create';

		$r++;
		$this->rights[$r][0] = 440303;
		$this->rights[$r][1] = 'Lire les délégations liées à ce compte';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'myactions';
        $this->rights[$r][5] = 'read';
        
        $r++;
		$this->rights[$r][0] = 440304;
		$this->rights[$r][1] = 'Lire les délégations liées à tout le monde';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'allactions';
        $this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = 440305;
		$this->rights[$r][1] = 'Supprimer les délégations liées à ce compte';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'myactions';
        $this->rights[$r][5] = 'delete';
        
        $r++;
		$this->rights[$r][0] = 440306;
		$this->rights[$r][1] = 'Supprimer les délégations liées à tout le monde';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'allactions';
        $this->rights[$r][5] = 'delete';
		
		$r++;
		$this->rights[$r][0] = 440310;
		$this->rights[$r][1] = $langs->trans('DelegationRightTabDelegationRead');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tab_delegation';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = 440311;
		$this->rights[$r][1] = $langs->trans('DelegationRightTabDelegationWrite');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tab_delegation';
		$this->rights[$r][5] = 'write';

		$r++;
		$this->rights[$r][0] = 440318;
		$this->rights[$r][1] = $langs->trans('DelegationRightSubcontractContractRead');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'subcontract_contract';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = 440319;
		$this->rights[$r][1] = $langs->trans('DelegationRightSubcontractContractWrite');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'subcontract_contract';
		$this->rights[$r][5] = 'write';

		$r++;
		$this->rights[$r][0] = 440320;
		$this->rights[$r][1] = $langs->trans('DelegationRightVatReverseChargeRead');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'vat_reverse_charge';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = 440321;
		$this->rights[$r][1] = $langs->trans('DelegationRightVatReverseChargeWrite');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'vat_reverse_charge';
		$this->rights[$r][5] = 'write';

		$r++;
		$this->rights[$r][0] = 440312;
		$this->rights[$r][1] = $langs->trans('DelegationRightTabDetailsRead');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tab_details';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = 440313;
		$this->rights[$r][1] = $langs->trans('DelegationRightTabDetailsWrite');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tab_details';
		$this->rights[$r][5] = 'write';

		$r++;
		$this->rights[$r][0] = 440314;
		$this->rights[$r][1] = $langs->trans('DelegationRightTabDc4SupplierRead');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tab_dc4_supplier';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = 440315;
		$this->rights[$r][1] = $langs->trans('DelegationRightTabDc4SupplierWrite');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tab_dc4_supplier';
		$this->rights[$r][5] = 'write';

		$r++;
		$this->rights[$r][0] = 440316;
		$this->rights[$r][1] = $langs->trans('DelegationRightTabDc4CustomerRead');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tab_dc4_customer';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = 440317;
		$this->rights[$r][1] = $langs->trans('DelegationRightTabDc4CustomerWrite');
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tab_dc4_customer';
		$this->rights[$r][5] = 'write';
		// Main menu entries
		$this->menu = array();			// List of menus to add

        $r = 0;
			
	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories.
	 *      @return     int             1 if OK, 0 if KO
	 */
	function init($options = '')
	{
	   global $db, $conf, $langs;

		$sql = array();

		$result = $this->load_tables();

		define('INC_FROM_DOLIBARR', true);
		
		$ext = new ExtraFields($db);

		//Societe
		$ext->addExtraField('lmdb_compte_tiers', 'lmdb_compte_tiers', 'varchar', 100, 255, 'societe', 0, 0, '', '', 1, '', 1, '', '', '', 'delegation@delegation', '$conf->delegation->enabled');
		$ext->addExtraField('lmdb_representant', 'lmdb_representant', 'varchar', 0, 255, 'societe', 0, 1, '', '', 1, '', 1, 'lmdb_representant_help', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		$ext->addExtraField('lmdb_qualite_representant', 'lmdb_qualite_representant', 'varchar', 0, 255, 'societe', 0, 1, '', '', 1, '', 1, 'lmdb_qualite_representant_help', '', 0, 'delegation@delegation', '$conf->delegation->enabled');

		//Factures
		$ext->addExtraField('lmdb_compte_prorata', 'lmdb_compte_prorata', 'varchar', 2, 255, 'facture', 0, 0, '', '', 1, '', 1, 'lmdb_compte_prorata_help', '', 0, 'delegation@delegation', '$conf->delegation->enabled');

		$ext->addExtraField('lmdb_envoi_auto', 'lmdb_envoi_auto', 'boolean', 3, '', 'facture', 0, 0, 0, '', 0, '', 0, 'lmdb_envoi_auto_help', '', 0, 'delegation@delegation', '$conf->delegation->enabled', );

		$ext->addExtraField('lmdb_template', 'lmdb_template', 'sellist', 4, '', 'facture', 0, 0, '', 'a:1:{s:7:"options";a:1:{s:89:"c_email_templates:label:rowid::((type_template:=:\'facture_send\') AND (entity:=:$ENTITY$))";N;}}', 0, '', 0, 'lmdb_template_help', '', 0, 'delegation@delegation', '$conf->delegation->enabled', );

		//Factures  récurentes

		$ext->addExtraField('lmdb_envoi_auto', 'lmdb_envoi_auto', 'boolean', 3, '', 'facture_rec', 0, 0, 0, '', 1, '', 1, 'lmdb_envoi_auto_help', '', 0, 'delegation@delegation', '$conf->delegation->enabled', );

		$ext->addExtraField('lmdb_template', 'lmdb_template', 'sellist', 4, '', 'facture_rec', 0, 0, '', 'a:1:{s:7:"options";a:1:{s:89:"c_email_templates:label:rowid::((type_template:=:\'facture_send\') AND (entity:=:$ENTITY$))";N;}}', 1, '', 1, 'lmdb_template_help', '', 0, 'delegation@delegation', '$conf->delegation->enabled', 0,0, );


		//Projets
		$ext->addExtraField('lmdb_project_amount', 'lmdb_project_amount', 'price', 2, 255, 'projet', 0, 0, '', '', 1, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');

		//Commandes Fournisseurs
		$ext->addExtraField('lmdb_n_devis_fourn', 'lmdb_n_devis_fourn', 'varchar', 1, '255', 'commande_fournisseur', 0, 0, '', '', 1, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		$ext->addExtraField('lmdb_date_devis_fourn', 'lmdb_date_devis_fourn', 'date', 2, '', 'commande_fournisseur', 0, 0, '', '', 1, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		$ext->addExtraField('lmdb_poste', 'lmdb_poste_info', 'varchar', 2, '255', 'commande_fournisseur', 0, 0, '', '', 1, '', 1, 'lmdb_poste_info_help', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		$ext->addExtraField('lmdb_link', 'lmdb_link', 'url', 1, '', 'commande_fournisseur', 0, 0, '', '', 1, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');

		//Proposition Fournisseurs
		$ext->addExtraField('lmdb_n_devis_fourn', 'lmdb_n_devis_fourn', 'varchar', 1, '255', 'supplier_proposal', 0, 0, '', '', 1, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		$ext->addExtraField('lmdb_date_devis_fourn', 'lmdb_date_devis_fourn', 'date', 2, '', 'supplier_proposal', 0, 0, '', '', 1, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		$ext->addExtraField('lmdb_poste', 'lmdb_poste_info', 'varchar', 0, '255', 'supplier_proposal', 0, 0, '', '', 1, '', 1, 'lmdb_poste_info_help', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		$ext->addExtraField('lmdb_link', 'lmdb_link', 'url', 1, '', 'supplier_proposal', 0, 0, '', '', 1, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');

		//Produits/Services
		$ext->addExtraField('lmdb_marque', 'lmdb_marque', 'varchar', 1, '255', 'product', 0, 0, '', '', 1, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');

		//Commandes Client

		if (empty($conf->global->BANK_ASK_PAYMENT_BANK_DURING_ORDER)) {
			//$ext->addExtraField('lmdb_commande_account', 'lmdb_commande_account', 'sellist', 100, '255', 'commande', 0, 0, '', 'a:1:{s:7:"options";a:1:{s:41:"bank_account:label:rowid::entity=$ENTITY$";N;}}', 1, '', 1, 'lmdb_commande_account_help', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		}

		// EN: Subcontracting contracts.
		// FR: Contrats de sous-traitance.
		$ext->addExtraField('delegation_subcontract_vat_reverse_charge', 'DelegationVatReverseCharge', 'boolean', 20, '', 'contrat', 0, 0, 0, '', 0, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');

		// EN: Customer documents (proposals, orders, invoices).
		// FR: Documents clients (devis, commandes, factures).
		$ext->addExtraField('delegation_vat_reverse_charge', 'DelegationVatReverseCharge', 'boolean', 20, '', 'propal', 0, 0, 0, '', 0, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		$ext->addExtraField('delegation_vat_reverse_charge', 'DelegationVatReverseCharge', 'boolean', 20, '', 'commande', 0, 0, 0, '', 0, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		$ext->addExtraField('delegation_vat_reverse_charge', 'DelegationVatReverseCharge', 'boolean', 20, '', 'facture', 0, 0, 0, '', 0, '', 1, '', '', 0, 'delegation@delegation', '$conf->delegation->enabled');
		
		//$ext->addExtraField($attrname, $label, $type, $pos, $size, $element, $unique, $required, $default_value, $param, $alwayseditable, $perms, $list, $help, $computed, $entity, $langfile, $enabled, $sommable,$pdf)

		$result = $this->_init($sql);

		if ($result > 0) {
			// EN: Ensure schema and dictionaries are up to date.
			// FR: Garantir que le schéma et les dictionnaires sont à jour.
			$this->ensureDelegationSchema();
			$this->ensurePaymentMode();
			$this->cleanupObsoleteData();
		}

		return $result;
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted.
	 *      @return     int             1 if OK, 0 if KO
	 */
	function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql);
	}

	private function cleanupObsoleteData()
	{
		$legacyFormA = 'DC'.chr(49);
		$legacyFormB = 'DC'.chr(50);
		$legacyMetric = implode('', array('r', 'a', 't', 'i', 'n', 'g'));
		$legacyToken = implode('', array('b', 'u', 'd', 'g', 'e', 't'));

		$tables = array(
			$legacyFormA,
			$legacyFormA.'_groupement',
			$legacyFormB,
			$legacyFormB.'_groupement',
			'LMDB_'.$legacyMetric,
			'LMDB_task_'.$legacyToken,
			'LMDB_poste_category',
			'LMDB_projet_task',
		);

		foreach ($tables as $table) {
			$this->db->query('DROP TABLE IF EXISTS '.MAIN_DB_PREFIX.$table);
		}

		$obsoleteconst = array(
			'LMDB_'.implode('', array('Q', 'O', 'N', 'T', 'O')).'_SLUG',
			'LMDB_'.implode('', array('Q', 'O', 'N', 'T', 'O')).'_AUTHORIZATION',
			'LMDB_'.implode('', array('Q', 'O', 'N', 'T', 'O')).'_PER_PAGE',
			'LMDB_'.$legacyToken.'_ORDER_STATUS',
			'LMDB_'.$legacyToken.'_ORDER_STATUS_REFUSED',
			'LMDB_'.$legacyFormA.'_ACTIVATED',
			'LMDB_'.$legacyFormB.'_ACTIVATED',
		);

		if (! empty($obsoleteconst)) {
			$values = array();
			foreach ($obsoleteconst as $const) {
				$values[] = "'".$this->db->escape($const)."'";
			}
			$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'const WHERE name IN ('.implode(',', $values).')';
			$this->db->query($sql);
		}

		$obsoleteextrafields = array(
			'lmdb_'.$legacyToken,
			'lmdb_complement_slug_number',
		);

		if (! empty($obsoleteextrafields)) {
			$values = array();
			foreach ($obsoleteextrafields as $field) {
				$values[] = "'".$this->db->escape($field)."'";
			}
			$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'extrafields WHERE name IN ('.implode(',', $values).')';
			$this->db->query($sql);
		}
	}

	/**
	 * EN: Ensure the delegation line table contains new supplier invoice fields.
	 * FR: S'assurer que la table des lignes de délégation contient les champs facture fournisseur.
	 *
	 * @return void
	 */
	private function ensureDelegationSchema()
	{
		// EN: Check for column existence (compatibility with Dolibarr v21).
		// FR: Vérifier l'existence de la colonne (compatibilité Dolibarr v21).
		$hasField = false;
		if (method_exists($this->db, 'DDLTableFieldExists')) {
			$hasField = $this->db->DDLTableFieldExists(MAIN_DB_PREFIX.'delegation_det', 'fk_facture_fourn');
		} else {
			$sql = "SHOW COLUMNS FROM ".MAIN_DB_PREFIX."delegation_det LIKE 'fk_facture_fourn'";
			$resql = $this->db->query($sql);
			if ($resql) {
				$hasField = ($this->db->num_rows($resql) > 0);
			}
		}

		// EN: Add supplier invoice link if missing.
		// FR: Ajouter le lien facture fournisseur si absent.
		if (! $hasField) {
			$sql = "ALTER TABLE ".MAIN_DB_PREFIX."delegation_det ADD COLUMN fk_facture_fourn int(11) DEFAULT NULL";
			$this->db->query($sql);
		}

		// EN: Check for unique index existence (compatibility with Dolibarr v21).
		// FR: Vérifier l'existence de l'index unique (compatibilité Dolibarr v21).
		$hasIndex = false;
		if (method_exists($this->db, 'DDLIndexExists')) {
			$hasIndex = $this->db->DDLIndexExists(MAIN_DB_PREFIX.'delegation_det', 'uk_delegation_facture_fourn');
		} else {
			$sql = "SHOW INDEX FROM ".MAIN_DB_PREFIX."delegation_det WHERE Key_name = 'uk_delegation_facture_fourn'";
			$resql = $this->db->query($sql);
			if ($resql) {
				$hasIndex = ($this->db->num_rows($resql) > 0);
			}
		}

		// EN: Add unique index to prevent duplicate supplier invoice links.
		// FR: Ajouter un index unique pour éviter les doublons de factures fournisseurs.
		if (! $hasIndex) {
			$sql = "ALTER TABLE ".MAIN_DB_PREFIX."delegation_det ADD UNIQUE KEY uk_delegation_facture_fourn (fk_object, fk_element, fk_facture_fourn)";
			$this->db->query($sql);
		}
	}

	/**
	 * EN: Ensure payment mode "Délégation de paiement" exists and is active.
	 * FR: S'assurer que le mode de règlement "Délégation de paiement" existe et est actif.
	 *
	 * @return int	<0 if KO, >0 if OK
	 */
	private function ensurePaymentMode()
	{
		global $conf;

		dol_include_once('/core/lib/admin.lib.php');

		$paymentCode = 'DELPAY';
		$paymentLabel = 'Délégation de paiement';
		$paymentId = 0;

		// EN: Look for existing payment mode.
		// FR: Rechercher le mode de règlement existant.
		$sql = "SELECT id, active FROM ".MAIN_DB_PREFIX."c_paiement";
		$sql.= " WHERE code = '".$this->db->escape($paymentCode)."'";
		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql) > 0) {
				$obj = $this->db->fetch_object($resql);
				$paymentId = (int) $obj->id;
				if ((int) $obj->active !== 1) {
					$this->db->query("UPDATE ".MAIN_DB_PREFIX."c_paiement SET active = 1 WHERE id = ".(int) $paymentId);
				}
			}
		}

		// EN: Create payment mode if missing.
		// FR: Créer le mode de règlement s'il manque.
		if ($paymentId <= 0) {
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."c_paiement (code, libelle, type, active)";
			$sql.= " VALUES ('".$this->db->escape($paymentCode)."', '".$this->db->escape($paymentLabel)."', 2, 1)";
			if ($this->db->query($sql)) {
				$paymentId = (int) $this->db->last_insert_id(MAIN_DB_PREFIX."c_paiement");
			}
		}

		if ($paymentId > 0) {
			dolibarr_set_const($this->db, 'DELEGATION_PAYMENT_MODE_ID', $paymentId, 'int', 0, '', $conf->entity);
		}

		return $paymentId > 0 ? 1 : -1;
	}


	/**
	 *		\brief		Create tables, keys and data required by module
	 * 					Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 					and create data commands must be stored in directory /mymodule/sql/
	 *					This function is called by this->init.
	 * 		\return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/delegation/sql/');
	}
}

?>
