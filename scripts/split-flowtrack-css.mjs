import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const flowtrackPath = resolve(projectRoot, 'resources/css/flowtrack.css');
const appPath = resolve(projectRoot, 'resources/css/app.css');
const outputDirectory = resolve(projectRoot, 'resources/css/generated');

export const flowtrackCssInputs = [
    'resources/css/generated/flowtrack-01.css',
    'resources/css/generated/flowtrack-02.css',
    'resources/css/generated/flowtrack-03.css',
    'resources/css/generated/flowtrack-04.css',
];

const byteLength = (value) => Buffer.byteLength(value, 'utf8');

const splitTopLevelUnits = (css) => {
    const units = [];
    let start = 0;
    let depth = 0;
    let quote = null;
    let escaped = false;
    let inComment = false;

    for (let index = 0; index < css.length; index += 1) {
        const character = css[index];
        const next = css[index + 1];

        if (inComment) {
            if (character === '*' && next === '/') {
                inComment = false;
                index += 1;
            }
            continue;
        }

        if (quote !== null) {
            if (escaped) {
                escaped = false;
            } else if (character === '\\') {
                escaped = true;
            } else if (character === quote) {
                quote = null;
            }
            continue;
        }

        if (character === '/' && next === '*') {
            inComment = true;
            index += 1;
            continue;
        }

        if (character === '"' || character === "'") {
            quote = character;
            continue;
        }

        if (character === '{') {
            depth += 1;
            continue;
        }

        if (character === '}') {
            depth -= 1;
            if (depth < 0) throw new Error('The source stylesheet has an unmatched closing brace.');
            if (depth === 0) {
                units.push(css.slice(start, index + 1));
                start = index + 1;
            }
        }
    }

    if (inComment || quote !== null || depth !== 0) {
        throw new Error('The source stylesheet ends inside a comment, string, or CSS block.');
    }

    if (start < css.length) units.push(css.slice(start));
    return units.filter((unit) => unit.length > 0);
};

const balanceUnits = (units, chunkCount) => {
    const chunks = [];
    let remainingBytes = units.reduce((sum, unit) => sum + byteLength(unit), 0);
    let remainingChunks = chunkCount;
    let current = '';

    for (const unit of units) {
        const idealBytes = remainingBytes / remainingChunks;
        const currentBytes = byteLength(current);
        const unitBytes = byteLength(unit);
        const canCloseChunk = current.length > 0 && chunks.length < chunkCount - 1;
        const beforeDistance = Math.abs(idealBytes - currentBytes);
        const afterDistance = Math.abs(idealBytes - currentBytes - unitBytes);

        if (canCloseChunk && currentBytes >= idealBytes * 0.72 && beforeDistance <= afterDistance) {
            chunks.push(current);
            remainingBytes -= currentBytes;
            remainingChunks -= 1;
            current = unit;
        } else {
            current += unit;
        }
    }

    chunks.push(current);
    if (chunks.length !== chunkCount || chunks.some((chunk) => chunk.length === 0)) {
        throw new Error(`Expected ${chunkCount} non-empty CSS chunks, generated ${chunks.length}.`);
    }
    return chunks;
};

export const splitFlowtrackCss = () => {
    const flowtrackCss = readFileSync(flowtrackPath, 'utf8');
    const appCss = readFileSync(appPath, 'utf8');
    const importPattern = /^@import\s+['"]\.\/flowtrack\.css['"];?\s*/;

    if (!importPattern.test(appCss)) {
        throw new Error('resources/css/app.css must begin with the flowtrack.css import.');
    }

    // This matches the original Vite cascade: flowtrack.css first, followed by
    // the small app.css additions that appeared after its @import directive.
    const combinedCss = `${flowtrackCss}\n${appCss.replace(importPattern, '')}`;
    const chunks = balanceUnits(splitTopLevelUnits(combinedCss), flowtrackCssInputs.length);

    if (chunks.join('') !== combinedCss) {
        throw new Error('Generated CSS does not reproduce the canonical stylesheet byte-for-byte.');
    }

    mkdirSync(outputDirectory, { recursive: true });
    flowtrackCssInputs.forEach((input, index) => {
        writeFileSync(resolve(projectRoot, input), chunks[index], 'utf8');
    });

    return chunks.map((chunk, index) => ({ input: flowtrackCssInputs[index], bytes: byteLength(chunk) }));
};

if (process.argv[1] && resolve(process.argv[1]) === fileURLToPath(import.meta.url)) {
    for (const chunk of splitFlowtrackCss()) {
        process.stdout.write(`${chunk.input}: ${chunk.bytes} bytes\n`);
    }
}
