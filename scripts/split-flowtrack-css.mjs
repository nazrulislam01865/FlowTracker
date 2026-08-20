import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const appPath = resolve(projectRoot, 'resources/css/app.css');
const outputDirectory = resolve(projectRoot, 'resources/css/generated');

export const flowtrackCssInputs = [
    'resources/css/generated/flowtrack-01.css',
    'resources/css/generated/flowtrack-02.css',
    'resources/css/generated/flowtrack-03.css',
    'resources/css/generated/flowtrack-04.css',
];

const byteLength = (value) => Buffer.byteLength(value, 'utf8');
const localImportPattern = /@import\s+(?:url\(\s*)?['"]([^'"]+)['"]\s*\)?\s*;/g;

const expandLocalImports = (filePath, stack = [], dependencies = new Set()) => {
    const absolutePath = resolve(filePath);
    if (stack.includes(absolutePath)) {
        const cycle = [...stack, absolutePath]
            .map((item) => item.replace(`${projectRoot}/`, ''))
            .join(' -> ');
        throw new Error(`Circular CSS import detected: ${cycle}`);
    }

    dependencies.add(absolutePath);
    const source = readFileSync(absolutePath, 'utf8');
    const nextStack = [...stack, absolutePath];

    const expanded = source.replace(localImportPattern, (statement, importTarget) => {
        if (!importTarget.startsWith('.')) {
            throw new Error(`Only relative CSS imports are allowed in the FlowTrack composition root: ${statement}`);
        }

        const importedPath = resolve(dirname(absolutePath), importTarget);
        return expandLocalImports(importedPath, nextStack, dependencies).css;
    });

    return { css: expanded, dependencies };
};

export const flowtrackCssDependencies = () => [...expandLocalImports(appPath).dependencies];

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
            if (depth < 0) throw new Error('The composed stylesheet has an unmatched closing brace.');
            if (depth === 0) {
                units.push(css.slice(start, index + 1));
                start = index + 1;
            }
        }
    }

    if (inComment || quote !== null || depth !== 0) {
        throw new Error('The composed stylesheet ends inside a comment, string, or CSS block.');
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
    const { css: composedCss } = expandLocalImports(appPath);
    const chunks = balanceUnits(splitTopLevelUnits(composedCss), flowtrackCssInputs.length);

    if (chunks.join('') !== composedCss) {
        throw new Error('Generated CSS does not reproduce the composed stylesheet byte-for-byte.');
    }

    mkdirSync(outputDirectory, { recursive: true });
    flowtrackCssInputs.forEach((input, index) => {
        writeFileSync(resolve(projectRoot, input), chunks[index], 'utf8');
    });

    return chunks.map((chunk, index) => ({
        input: flowtrackCssInputs[index],
        bytes: byteLength(chunk),
    }));
};

if (process.argv[1] && resolve(process.argv[1]) === fileURLToPath(import.meta.url)) {
    for (const chunk of splitFlowtrackCss()) {
        process.stdout.write(`${chunk.input}: ${chunk.bytes} bytes\n`);
    }
}
