tinyMCE_Full = {

	selector						: "#articleArea",
	width							: "100%",
	height							: "500px",
	document_base_url				: "../../../",
	content_css						: "assets/third-party/bootstrap/css/bootstrap.min.css,assets/css/main.css",
	plugins							: [ "paste visualchars visualblocks textcolor colorpicker lists advlist image contextmenu anchor link charmap media hr pagebreak table code codesample searchreplace" ],
	menubar							: "",
	block_formats					: "Introduction=intro;Section=section;Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 3=h4;Heading 3=h5",
	contextmenu						: "cut copy paste pastetext | selectall searchreplace | link image inserttable",
	contextmenu_never_use_native	: true,
	formats: {
		intro: {block : "section", attributes : {id : "introduction"}},
		section: {block : "section"},
	},
	rel_list: [

		{
			title: "Default Link",
			value: "default"
		},

		{
			title: "External Link",
			value: "external"
		},

		{
			title: "Lightbox Image",
			value: "lightbox"
		}

	],
	toolbar1: "formatselect | fontselect | fontsizeselect | forecolor backcolor | cut copy paste | searchreplace | undo redo",
	toolbar2: "bold italic underline strikethrough subscript superscript | removeformat | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent",
	toolbar3: "link unlink | anchor image media codesample | table blockquote hr charmap codesample| visualchars visualblocks nonbreaking pagebreak | code",

};