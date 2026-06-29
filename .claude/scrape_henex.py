#!/usr/bin/env python3
"""Scrape ALL products from henex.cn (English). Downloads images + PDFs, saves JSON."""

import requests
from bs4 import BeautifulSoup
import json, os, re, time

BASE = "https://www.henex.cn"
HEADERS = {"User-Agent": "Mozilla/5.0 (compatible; HenexScraper/1.0)", "Accept-Language": "en-US,en;q=0.9"}
PROJECT = "/sessions/admiring-modest-knuth/mnt/Henex"
IMG_DIR = f"{PROJECT}/public/images/products"
DOC_DIR = f"{PROJECT}/public/docs"
os.makedirs(IMG_DIR, exist_ok=True)
os.makedirs(DOC_DIR, exist_ok=True)

def get(url, retries=3):
    for i in range(retries):
        try:
            r = requests.get(url, headers=HEADERS, timeout=20)
            r.raise_for_status()
            return r
        except Exception as e:
            print(f"  Retry {i+1} {url}: {e}")
            time.sleep(2)
    return None

def download_file(url, dest):
    if os.path.exists(dest):
        return True
    r = get(url)
    if r and r.content:
        with open(dest, 'wb') as f:
            f.write(r.content)
        return True
    return False

def slug(text):
    return re.sub(r'[^a-z0-9]+', '-', text.lower()).strip('-')

def scrape_product(url):
    r = get(url)
    if not r:
        return None
    soup = BeautifulSoup(r.text, 'html.parser')

    # --- Name / SKU ---
    name, subtitle = '', ''
    for sel in ['.show_name', 'h1', '.product-name', '.title']:
        el = soup.select_one(sel)
        if el:
            name = el.get_text(strip=True)
            break
    for sel in ['.show_en_name', '.show_type', '.product-subtitle']:
        el = soup.select_one(sel)
        if el:
            subtitle = el.get_text(strip=True)
            break

    # fallback: page title
    if not name:
        title = soup.title.get_text(strip=True) if soup.title else ''
        name = title.split('_')[0].strip()

    sku = name

    # --- Category breadcrumb ---
    crumbs = [a.get_text(strip=True) for a in soup.select('nav a, .breadcrumb a, .crumb a')]
    category = crumbs[-2] if len(crumbs) >= 2 else ''
    subcategory = crumbs[-1] if len(crumbs) >= 1 else ''

    # --- Gallery images ---
    images = []
    for img in soup.find_all('img'):
        src = img.get('src', '')
        if 'uploadfile' in src:
            full = (BASE + src) if src.startswith('/') else src
            if full not in images:
                images.append(full)

    # --- Features ---
    features = []
    feat_el = soup.find(string=re.compile(r'Feature', re.I))
    if feat_el:
        container = feat_el.find_parent()
        while container and container.name not in ('div','section','article'):
            container = container.find_parent()
        if container:
            for li in container.find_all(['li','p']):
                t = li.get_text(strip=True)
                if t and len(t) > 4 and t not in features:
                    features.append(t)

    # --- Application area ---
    app_area = ''
    app_el = soup.find(string=re.compile(r'Application area', re.I))
    if app_el:
        nxt = app_el.find_parent()
        if nxt:
            sib = nxt.find_next_sibling()
            app_area = sib.get_text(strip=True) if sib else ''

    # --- Spec images ---
    spec_imgs = []
    spec_el = soup.find(string=re.compile(r'TECHNICAL SPECIFICATION', re.I))
    if spec_el:
        container = spec_el.find_parent()
        while container and container.name not in ('div','section','article','table'):
            container = container.find_parent()
        if container:
            parent2 = container.find_parent()
            scope = parent2 if parent2 else container
            for img in scope.find_all('img'):
                src = img.get('src','')
                if 'uploadfile' in src:
                    full = (BASE + src) if src.startswith('/') else src
                    if full not in spec_imgs and full not in images:
                        spec_imgs.append(full)

    # --- PDF ---
    pdf_url = ''
    for a in soup.find_all('a', href=True):
        href = a['href']
        if 'file' in href and ('down' in href or '.pdf' in href.lower()):
            pdf_url = (BASE + href) if href.startswith('/') else href
            break

    # --- Download images ---
    sku_dir = f"{IMG_DIR}/{slug(sku) or 'product'}"
    os.makedirs(sku_dir, exist_ok=True)
    local_images = []
    for i, img_url in enumerate(images[:10]):
        ext = (img_url.split('.')[-1] or 'jpg')[:4]
        dest = f"{sku_dir}/{i}.{ext}"
        if download_file(img_url, dest):
            local_images.append(f"images/products/{slug(sku)}/{i}.{ext}")

    local_specs = []
    for i, img_url in enumerate(spec_imgs[:4]):
        ext = (img_url.split('.')[-1] or 'jpg')[:4]
        dest = f"{sku_dir}/spec_{i}.{ext}"
        if download_file(img_url, dest):
            local_specs.append(f"images/products/{slug(sku)}/spec_{i}.{ext}")

    # --- Download PDF ---
    local_pdf = ''
    if pdf_url:
        pdf_name = f"{slug(sku)}.pdf"
        dest = f"{DOC_DIR}/{pdf_name}"
        if download_file(pdf_url, dest):
            local_pdf = f"docs/{pdf_name}"

    return {
        'sku': sku,
        'slug': slug(sku),
        'name': name,
        'subtitle': subtitle,
        'category': category,
        'subcategory': subcategory,
        'features': features,
        'application_area': app_area,
        'images': local_images,
        'spec_images': local_specs,
        'pdf': local_pdf,
        'source_url': url,
    }

# ====== Collect all product URLs ======
print("=== Collecting product URLs ===")
ALL_URLS = []
for page in range(1, 15):
    url = f"{BASE}/index.php?c=category&id=2&page={page}"
    r = get(url)
    if not r:
        break
    soup = BeautifulSoup(r.text, 'html.parser')
    links = soup.find_all('a', href=re.compile(r'c=show&id=\d+'))
    if not links:
        print(f"  Page {page}: no products, stopping")
        break
    new = 0
    for a in links:
        href = a['href']
        full = (BASE + href) if href.startswith('/') else href
        if full not in ALL_URLS:
            ALL_URLS.append(full)
            new += 1
    print(f"  Page {page}: {new} new → total {len(ALL_URLS)}")
    time.sleep(0.4)

print(f"\nTotal: {len(ALL_URLS)} products\n")

# ====== Scrape each product ======
products = []
for i, url in enumerate(ALL_URLS, 1):
    print(f"[{i}/{len(ALL_URLS)}] {url}")
    p = scrape_product(url)
    if p:
        products.append(p)
        print(f"   SKU={p['sku']}  imgs={len(p['images'])}  specs={len(p['spec_images'])}  pdf={bool(p['pdf'])}")
    time.sleep(0.5)

# ====== Save JSON ======
out = f"{PROJECT}/.claude/henex_products.json"
with open(out, 'w', encoding='utf-8') as f:
    json.dump(products, f, ensure_ascii=False, indent=2)

print(f"\n=== DONE: {len(products)} products → {out} ===")
