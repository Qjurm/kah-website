// node test.js
function testParse(filename) {
    const nameWithoutExt = filename.replace(/\.pdf$/i, '');
    let inferredTitle = nameWithoutExt;
    let inferredInstrumentStr = '';

    const underscoreIndex = nameWithoutExt.lastIndexOf('_');
    if (underscoreIndex !== -1) {
        inferredTitle = nameWithoutExt.substring(0, underscoreIndex).trim();
        inferredInstrumentStr = nameWithoutExt.substring(underscoreIndex + 1).trim();
    } else {
        const dashIndex = nameWithoutExt.lastIndexOf('-');
        if (dashIndex !== -1) {
            inferredTitle = nameWithoutExt.substring(0, dashIndex).trim();
            inferredInstrumentStr = nameWithoutExt.substring(dashIndex + 1).trim();
        }
    }

    let parsedName = inferredInstrumentStr.toLowerCase().replace(/\s*\d+$/, '');

    console.log(`Original: "${filename}"`);
    console.log(`  Parsed Title: "${inferredTitle}"`);
    console.log(`  Parsed Instrument: "${inferredInstrumentStr}"`);
    console.log(`  Stripped Alias: "${parsedName}"`);
}

testParse("Tercio de Quites_Klarinet Bas 2.pdf");
testParse("Tercio de Quites_Saxofoon Alt 1.pdf");
testParse("Tercio de Quites_Slagwerk Percussion 1.pdf");
testParse("Tercio de Quites - Trompet 1.pdf");
testParse("NoUnderscoreOrDash.pdf");
