import fs from 'node:fs/promises';
import path from 'node:path';
import { createRequire } from 'node:module';
import { compile } from 'tailwindcss';
import { Scanner } from '@tailwindcss/oxide';
const require = createRequire(import.meta.url);
const root = path.resolve(import.meta.dirname, '..');
const sourceRoot = path.join(root, 'resources/css/design');
async function files(dir) {
    const entries = await fs.readdir(dir, {withFileTypes:true});
    return (await Promise.all(entries.map(e => e.isDirectory() ? files(path.join(dir,e.name)) : [path.join(dir,e.name)]))).flat();
}
async function loadStylesheet(id, base) {
    const file = id.startsWith('.') ? path.resolve(base,id) : require.resolve(id);
    return {path:file,base:path.dirname(file),content:await fs.readFile(file,'utf8')};
}
async function save(file, css) {
    // Atomic replacement keeps requests from reading a partially written stylesheet.
    await fs.mkdir(path.dirname(file),{recursive:true});
    const temp=file+'.tmp'; await fs.writeFile(temp,css); await fs.rename(temp,file);
}
async function build() {
    const sources = (await files(sourceRoot)).filter(f=>f.endsWith('.css'));
    for (const file of sources) {
        const compiler=await compile(await fs.readFile(file,'utf8'),{base:path.dirname(file),loadStylesheet});
        await save(path.join(root,'public/css',path.relative(sourceRoot,file)),compiler.build([]));
    }
    const utilitySource=path.join(root,'resources/css/design-utilities.css');
    const compiler=await compile(await fs.readFile(utilitySource,'utf8'),{base:path.dirname(utilitySource),loadStylesheet});
    const scanner=new Scanner({sources:[
        {base:root,pattern:'resources/views/**/*.blade.php',negated:false},
        {base:root,pattern:'public/js/**/*.js',negated:false}
    ]});
    await save(path.join(root,'public/css/design-utilities.css'),compiler.build(scanner.scan()));
    console.log(`Tailwind design built: ${sources.length} component stylesheets and shared utilities.`);
}
await build();
if(process.argv.includes('--watch')) {
    // Poll source contents so this also works reliably with Windows/Herd file watchers.
    let previous=''; let busy=false;
    setInterval(async()=>{
        if(busy)return; busy=true;
        try {
            const watched=[...(await files(sourceRoot)),...(await files(path.join(root,'resources/views'))),...(await files(path.join(root,'public/js'))),path.join(root,'resources/css/design-utilities.css')];
            const signature=(await Promise.all(watched.map(async f=>f+':'+(await fs.stat(f)).mtimeMs))).join('|');
            if(previous && signature!==previous)await build();
            previous=signature;
        } catch(error){console.error('Tailwind build failed:',error.message);} finally{busy=false;}
    },1000);
    console.log('Watching Tailwind design sources, Blade views and portal JavaScript.');
}
