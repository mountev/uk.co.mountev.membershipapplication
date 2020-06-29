{* HEADER *}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

<div class="crm-section">
  <div class="label">{$form.membership_type_ids.label}</div>
  <div class="content">{$form.membership_type_ids.html}</div>
  <div class="clear"></div>
</div>
<div class="crm-section">
  <div class="label">{$form.is_override.label}</div>
  <div class="content">{$form.is_override.html}</div>
  <div class="clear"></div>
</div>
<div class="crm-section {$form.membership_status_to.name}-section">
  <div class="label">{$form.membership_status_to.label}</div>
  <div class="content">{$form.membership_status_to.html}</div>
  <div class="clear"></div>
</div>
<div class="crm-section">
  <div class="label">{$form.is_course_end_date.label}</div>
  <div class="content">{$form.is_course_end_date.html}</div>
  <div class="clear"></div>
</div>
<div class="crm-section">
  <div class="label">{$form.membership_type_tags.label}</div>
  <div class="content">{$form.membership_type_tags.html}&nbsp;&nbsp;{$form.tag_membership_type_ids.label}&nbsp;&nbsp;{$form.tag_membership_type_ids.html}</div>
  <div class="clear"></div>
</div>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
<script type="text/javascript">
  CRM.$(function($) {
    showHide($("input[name='is_override']:checked").val());
    $("input[name='is_override']").change(function() {
      showHide($(this).val());
    });
    function showHide(val) {
      if (val == 1) {
        $('#membership_status_to').removeAttr('disabled');
      } else {
        $('#membership_status_to').val('');
        $('#membership_status_to').attr('disabled', true);
      }
    }
  });
</script>
{/literal}
