		<table id="listing">
			<thead>
				<tr>
					<td colspan="7" class="right">
						<form action="gestion_mailings.php" method="get" id="historyform">
							<span>
								<label for="nbshow">{_T string="Records per page:"}</label>
								<select name="nbshow" id="nbshow">
									{html_options options=$nbshow_options selected=$numrows}
								</select>
								<noscript> <span><input type="submit" value="{_T string="Change"}" /></span></noscript>
							</span>
						</form>
					</td>
				</tr>
				<tr>
					<th class="listing small_head">#</th>
					<th class="listing left date_row">
						<a href="?tri=date_log" class="listing">
							{_T string="Date"}
							{if $history->orderby eq "date_log"}
								{if $history->getDirection() eq "DESC"}
							<img src="{$template_subdir}images/down.png" width="10" height="6" alt="{_T string="Ascendent"}"/>
								{else}
							<img src="{$template_subdir}images/up.png" width="10" height="6" alt="{_T string="Descendant"}"/>
								{/if}
							{/if}
						</a>
					</th>
					<th class="listing left username_row">
						<a href="?tri=adh_log" class="listing">
							{_T string="User"}
							{if $history->orderby eq "adh_log"}
								{if $history->getDirection() eq "DESC"}
							<img src="{$template_subdir}images/down.png" width="10" height="6" alt="{_T string="Ascendent"}"/>
								{else}
							<img src="{$template_subdir}images/up.png" width="10" height="6" alt="{_T string="Descendant"}"/>
								{/if}
							{/if}
						</a>
					</th>
                    <th class="listing left username_row">
                        {_T string="Recipients"}
                    </th>
					<th class="listing left">
						<a href="?tri=action_log" class="listing">
							{_T string="Subject"}
							{if $history->orderby eq "action_log"}
								{if $history->getDirection() eq "DESC"}
							<img src="{$template_subdir}images/down.png" width="10" height="6" alt="{_T string="Ascendent"}"/>
								{else}
							<img src="{$template_subdir}images/up.png" width="10" height="6" alt="{_T string="Descendant"}"/>
								{/if}
							{/if}
						</a>
					</th>
                    {*<th class="listing">{_T string="Synopsis"}</th>*}
                    <th class="listing"></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6" class="center">
						{_T string="Pages:"}<br/>
						<ul class="pages">{$pagination}</ul>
					</td>
				</tr>
			</tfoot>
			<tbody>
{foreach from=$logs item=log name=eachlog}
				<tr class="tbl_line_{if $smarty.foreach.eachlog.iteration % 2 eq 0}even{else}odd{/if}">
					<td class="center">{$smarty.foreach.eachlog.iteration}</td>
					<td class="nowrap">{$log.mailing_date|date_format:"%a %d/%m/%Y - %R"}</td>
					<td>{if $log.mailing_sender eq 0}Admin{else}{$log.mailing_sender}{/if}</td>
					<td>{$log.mailing_recipients|unserialize|@count}</td>
					<td>{$log.mailing_subject}</td>
					{*<td>{$log.mailing_body_resume}</td>*}
					<td class="center nowrap actions_row">
                        <a href="mailing_adherents.php?from={$log.mailing_id}">
                            <img
                                src="{$template_subdir}images/icon-mail.png"
                                alt="{_T string="New mailing from %s" pattern="/%s/" replace=$log.mailing_id}"
                                width="16"
                                height="16"
                                title="{_T string="Use mailing '%subject' as a template for a new one" pattern="/%subject/" replace=$log.mailing_subject}"
                                />
                        </a>
						<a href="ajouter_adherent.php?id_adh={$mailing->id}">
                            <img
                                src="{$template_subdir}images/icon-edit.png"
                                alt="{_T string="[mod]"}"
                                width="16"
                                height="16"
                                title="{_T string="%membername: edit informations" pattern="/%membername/" replace=$member->sname}"
                                />
                        </a>
						<a
                            onclick="return confirm('{_T string="Do you really want to delete this mailing from the base?"|escape:"javascript"}')"
                            href="?sup={$log.mailing_id}">
                            <img src="{$template_subdir}images/icon-trash.png" alt="{_T string="[del]"}" width="16" height="16"/>
                        </a>
					</td>
				</tr>
{foreachelse}
				<tr><td colspan="5" class="emptylist">{_T string="No sent mailing has been stored in the database yet."}</td></tr>
{/foreach}
			</tbody>
		</table>
		<script type="text/javascript">
            $('#nbshow').change(function() {ldelim}
                this.form.submit();
            {rdelim});
		</script>