// src/blocks/index.js
import "./info-box";
// Importar aquí otros bloques personalizados

// src/blocks/info-box/index.js
const { registerBlockType } = wp.blocks;
const { RichText, InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl } = wp.components;
const { __ } = wp.i18n;

import "./editor.scss";

registerBlockType("wp-tailwind-blocks/info-box", {
  title: __("Info Box", "wp-tailwind-blocks"),
  icon: "lightbulb",
  category: "wp-tailwind-blocks",
  attributes: {
    title: {
      type: "string",
      source: "html",
      selector: "h3",
      default: __("Info Box Title", "wp-tailwind-blocks"),
    },
    content: {
      type: "string",
      source: "html",
      selector: "p",
      default: __("Info Box Content", "wp-tailwind-blocks"),
    },
    backgroundColor: {
      type: "string",
      default: "#f3f4f6",
    },
    textColor: {
      type: "string",
      default: "#1f2937",
    },
    boxType: {
      type: "string",
      default: "info",
    },
  },

  edit: (props) => {
    const {
      attributes: { title, content, backgroundColor, textColor, boxType },
      setAttributes,
    } = props;

    const blockProps = useBlockProps({
      className: `p-6 rounded-lg shadow-md mb-6 ${
        boxType === "info"
          ? "bg-blue-50"
          : boxType === "warning"
          ? "bg-yellow-50"
          : boxType === "error"
          ? "bg-red-50"
          : boxType === "success"
          ? "bg-green-50"
          : ""
      }`,
    });

    // Actualizar título
    const onChangeTitle = (newTitle) => {
      setAttributes({ title: newTitle });
    };

    // Actualizar contenido
    const onChangeContent = (newContent) => {
      setAttributes({ content: newContent });
    };

    return (
      <>
        <InspectorControls>
          <PanelBody
            title={__("Box Settings", "wp-tailwind-blocks")}
            initialOpen={true}
          >
            <SelectControl
              label={__("Box Type", "wp-tailwind-blocks")}
              value={boxType}
              options={[
                { label: __("Info", "wp-tailwind-blocks"), value: "info" },
                {
                  label: __("Warning", "wp-tailwind-blocks"),
                  value: "warning",
                },
                { label: __("Error", "wp-tailwind-blocks"), value: "error" },
                {
                  label: __("Success", "wp-tailwind-blocks"),
                  value: "success",
                },
              ]}
              onChange={(value) => setAttributes({ boxType: value })}
            />

            <div className="mt-4">
              <p>{__("Background Color", "wp-tailwind-blocks")}</p>
              <ColorPalette
                value={backgroundColor}
                onChange={(color) => setAttributes({ backgroundColor: color })}
              />
            </div>

            <div className="mt-4">
              <p>{__("Text Color", "wp-tailwind-blocks")}</p>
              <ColorPalette
                value={textColor}
                onChange={(color) => setAttributes({ textColor: color })}
              />
            </div>
          </PanelBody>
        </InspectorControls>

        <div {...blockProps}>
          <RichText
            tagName="h3"
            className="text-lg font-bold mb-2"
            value={title}
            onChange={onChangeTitle}
            placeholder={__("Enter title...", "wp-tailwind-blocks")}
            style={{ color: textColor }}
          />
          <RichText
            tagName="p"
            className="text-base"
            value={content}
            onChange={onChangeContent}
            placeholder={__("Enter content...", "wp-tailwind-blocks")}
            style={{ color: textColor }}
          />
        </div>
      </>
    );
  },

  save: (props) => {
    const {
      attributes: { title, content, backgroundColor, textColor, boxType },
    } = props;

    const blockProps = useBlockProps.save({
      className: `p-6 rounded-lg shadow-md mb-6 ${
        boxType === "info"
          ? "bg-blue-50"
          : boxType === "warning"
          ? "bg-yellow-50"
          : boxType === "error"
          ? "bg-red-50"
          : boxType === "success"
          ? "bg-green-50"
          : ""
      }`,
    });

    return (
      <div {...blockProps}>
        <RichText.Content
          tagName="h3"
          className="text-lg font-bold mb-2"
          value={title}
          style={{ color: textColor }}
        />
        <RichText.Content
          tagName="p"
          className="text-base"
          value={content}
          style={{ color: textColor }}
        />
      </div>
    );
  },
});
