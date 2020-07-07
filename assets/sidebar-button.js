const { registerPlugin } = wp.plugins;
const { PluginMoreMenuItem } = wp.editPost;

const ExportOnePostButton = () => (
  <PluginMoreMenuItem icon="download">
    <a href={exportOne.export_url}>Export This</a>
  </PluginMoreMenuItem>
);

registerPlugin("export-one-post", { render: ExportOnePostButton });
