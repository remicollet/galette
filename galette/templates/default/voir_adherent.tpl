	<h1 id="titre">{_T string="Member Profile"}</h1>
    {if $error_detected|@count != 0}
    		<div id="errorbox">
    			<h1>{_T string="- ERROR -"}</h1>
    			<ul>
    {foreach from=$error_detected item=error}
    				<li>{$error}</li>
    {/foreach}
    			</ul>
    		</div>
    {/if}
	<div class="bigtable">

	<ul id="details_menu">
{if ($data.pref_card_self eq 1) or ($smarty.session.admin_status eq 1)}
		<li>
			<a href="carte_adherent.php?id_adh={$data.id_adh}" id="btn_membercard">{_T string="Generate Member Card"}</a>
		</li>
{/if}
		<li>
			<a href="ajouter_adherent.php?id_adh={$data.id_adh}" id="btn_edit">{_T string="Modification"}</a>
		</li>
		<li>
			<a href="gestion_contributions.php?id_adh={$data.id_adh}" id="btn_contrib">{_T string="View contributions"}</a>
		</li>
{if $smarty.session.admin_status eq 1}
		<li>
			<a href="ajouter_contribution.php?id_adh={$data.id_adh}" id="btn_addcontrib">{_T string="Add a contribution"}</a>
		</li>
{/if}
	</ul>

		<table class="details">
			<caption>{_T string="Identity:"}</caption>
			<tr>
				<th>{_T string="Name:"}</th>
				<td>{$data.titre_adh} {$data.nom_adh} {$data.prenom_adh}</td>
				<td rowspan="5" class="photo"><img src="picture.php?id_adh={$data.id_adh}&amp;rand={$time}" class="picture" width="{$data.picture_width}" height="{$data.picture_height}" alt="{_T string="Picture"}"/></td>
			</tr>
			<tr>
				<th>{_T string="Nickname:"}</th>
				<td>{$data.pseudo_adh}</td>
			</tr> 
			<tr> 
				<th>{_T string="birth date:"}</th>
				<td>{$data.ddn_adh}</td>
			</tr>
			<tr>
				<th>{_T string="Profession:"}</th>
				<td>{$data.prof_adh}</td>
			</tr>
			<tr>
				<th>{_T string="Language:"}</th>
				<td><img src="{$data.pref_lang_img}" alt=""/> {$data.pref_lang}</td>
			</tr>
		</table>

		<table class="details">
			<caption>{_T string="Galette-related data:"}</caption>
			<tr>
				<th>{_T string="Status:"}</th>
				<td>{$data.libelle_statut}</td>
			</tr>
			<tr>
				<th>{_T string="Be visible in the<br /> members list :"}</th>
				<td>{$data.bool_display_info}</td>
			</tr>
{if $smarty.session.admin_status eq 1}
			<tr>
				<th>{_T string="Account:"}</th>
				<td>{$data.activite_adh}</td>
			</tr>
			<tr>
				<th>{_T string="Galette Admin:"}</th>
				<td>{$data.bool_admin_adh}</td>
			</tr>
			<tr>
				<th>{_T string="Freed of dues:"}</th>
				<td>{$data.bool_exempt_adh}</td>
			</tr>
{/if}
			<tr>
				<th>{_T string="Username:"}</th>
				<td>{$data.login_adh}</td>
			</tr>
{if $smarty.session.admin_status eq 1}
			<tr>
				<th>{_T string="Creation date:"}</th>
				<td>{$data.date_crea_adh}</td>
			</tr>
			<tr>
				<th>{_T string="Other informations (admin):"}</th>
				<td>{$data.info_adh}</td>
			</tr>
{/if}
		</table>

		<table class="details">
			<caption>{_T string="Contact information:"}</caption>
			<tr>
				<th>{_T string="Address:"}</th> 
				<td>
					{$data.adresse_adh}
{if $data.adresse2_adh ne ''}
					<br/>{$data.adresse2_adh}
{/if}
				</td>
			</tr>
			<tr>
				<th>{_T string="Zip Code:"}</th>
				<td>{$data.cp_adh}</td>
			</tr>
			<tr>
				<th>{_T string="City:"}</th>
				<td>{$data.ville_adh}</td>
			</tr>
			<tr>
				<th>{_T string="Country:"}</th>
				<td>{$data.pays_adh}</td>
			</tr>
			<tr>
				<th>{_T string="Phone:"}</th>
				<td>{$data.tel_adh}</td>
			</tr>
			<tr>
				<th>{_T string="Mobile phone:"}</th>
				<td>{$data.gsm_adh}</td>
			</tr>
			<tr>
				<th>{_T string="E-Mail:"}</th>
				<td>
{if $data.email_adh ne ''}					
					<a href="mailto:{$data.email_adh}">{$data.email_adh}</a>
{/if}
				</td>
			</tr>
			<tr>
				<th>{_T string="Website:"}</th>
				<td>
{if $data.url_adh ne ''}
					<a href="{$data.url_adh}">{$data.url_adh}</a>
{/if}						
				</td>
			</tr>
			<tr>
				<th>{_T string="ICQ:"}</th>
				<td>{$data.icq_adh}</td>
			</tr>
			<tr>
				<th>{_T string="Jabber:"}</th>
				<td>{$data.jabber_adh}</td>
			</tr>
			<tr>
				<th>{_T string="MSN:"}</th>
				<td>
{if $data.msn_adh ne ''}
					<a href="mailto:{$data.msn_adh}">{$data.msn_adh}</a>
{/if}
				</td>
			</tr>
			<tr>
				<th>{_T string="Id GNUpg (GPG):"}</th>
				<td>{$data.gpgid}</td>
			</tr>
			<tr>
				<th>{_T string="fingerprint:"}</th>
				<td>{$data.fingerprint}</td>
			</tr>
		</table>

{include file="display_dynamic_fields.tpl" is_form=false}
	</div>