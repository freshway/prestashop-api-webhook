<div class="novi-header">
	<div class="container">
		<div class="novi-header-cnt row">
			<div class="col-xs-12 col-sm-9 col-md-9">
				<h1 class="title-page">
					{if $p['title'] == null}
						{$page.meta.title}
					{else}
					{$p['title']}
					{/if}
					
				</h1>
			</div>
			<div class="col-xs-12 col-sm-3 col-md-3">
				<div class="image-page">
					<img src="{$dir_images}{$p['name']}.png">
				</div>
			</div>
		</div>
	</div>
</div>

<style type="text/css">
	{literal}
	.breadcrumb ol {
	    margin: 40px 0 0;
	}
	.breadcrumb {
    	margin-bottom: 40px;
    }
	{/literal}
</style>