function formatXml(data) {
    const parser = new DOMParser();
    const xml = parser.parseFromString(data, 'application/xml');

    const xslt = `
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>
  <xsl:strip-space elements="*"/>
  <xsl:template match="@*|node()">
    <xsl:copy>
      <xsl:apply-templates select="@*|node()"/>
    </xsl:copy>
  </xsl:template>
</xsl:stylesheet>`;

    const xsltDoc = parser.parseFromString(xslt, 'application/xml');
    const processor = new XSLTProcessor();
    processor.importStylesheet(xsltDoc);

    const resultDoc = processor.transformToDocument(xml);
    return new XMLSerializer().serializeToString(resultDoc);
}
