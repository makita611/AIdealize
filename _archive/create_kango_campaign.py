# -*- coding: utf-8 -*-
"""
はなまるAI看護版 Google Ads キャンペーン作成スクリプト
"""
import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

from google.ads.googleads.client import GoogleAdsClient
from google.ads.googleads.errors import GoogleAdsException
from datetime import date

CUSTOMER_ID = "2534507200"
YAML_PATH   = r"C:\Users\hm60m\Documents\code\hanamaru\google-ads.yaml"
LP_URL      = "https://aidealize.com/lp/hanamaru-kango/"

# ============================================================
# キーワード定義
# ============================================================
KEYWORDS_A = [
    ("訪問看護 記録 効率化",   "PHRASE"),
    ("看護記録 自動作成",       "PHRASE"),
    ("看護記録 時間短縮",       "PHRASE"),
    ("SOAP 自動作成",           "PHRASE"),
    ("看護記録 AI",             "PHRASE"),
    ("訪問看護 残業 解決",      "PHRASE"),
    ("訪問看護記録効率化",      "EXACT"),
    ("看護記録自動作成",        "EXACT"),
    ("看護記録AI",              "EXACT"),
    ("SOAP自動作成",            "EXACT"),
]

KEYWORDS_B = [
    ("看護記録 音声入力",   "PHRASE"),
    ("訪問看護 ICT",        "PHRASE"),
    ("訪問看護 DX",         "PHRASE"),
    ("看護師 記録 効率化",  "PHRASE"),
    ("看護記録音声入力",    "EXACT"),
    ("訪問看護ICT",         "EXACT"),
    ("訪問看護DX",          "EXACT"),
]

# ============================================================
# 広告文（グループA）
# ============================================================
HEADLINES_A = [
    "訪問看護の記録が5分で完了",
    "LINE音声入力でSOAP自動生成",
    "1ヶ月無料トライアル実施中",
    "残業を今月からゼロにする",
    "訪問看護専用AIが解決",
    "看護記録を80%時間削減",
    "SOAPノートが話すだけで完成",
    "アプリ不要LINEだけで完結",
    "訪問看護記録の悩みを解決",
]

DESCRIPTIONS_A = [
    "毎日30〜60分の看護記録をAIが自動生成。LINEで話すだけでSOAPノートが完成します。",
    "訪問看護専用AI。モニター3社限定・初期費用半額。1ヶ月無料で今すぐお試しください。",
]

# ============================================================
# 広告文（グループB）
# ============================================================
HEADLINES_B = [
    "音声入力で看護記録が完成",
    "LINEで話すだけSOAP自動化",
    "訪問看護のDX今すぐ始める",
    "1ヶ月無料トライアル実施中",
    "看護師の残業を大幅削減",
    "訪問看護ICT導入を簡単に",
    "アプリ不要LINEだけで完結",
    "看護記録80%削減のAIツール",
    "無料トライアルで効果を実感",
]

DESCRIPTIONS_B = [
    "音声メッセージをLINEで送るだけ。AIが看護記録を自動作成。訪問看護に特化したICTツール。",
    "導入簡単・アプリ不要。訪問看護ステーション向けDXを1ヶ月無料でお試しいただけます。",
]

# ============================================================
# ヘルパー関数
# ============================================================
def create_budget(client):
    svc = client.get_service("CampaignBudgetService")
    op  = client.get_type("CampaignBudgetOperation")
    b   = op.create
    import time; b.name = f"はなまるAI看護版_予算_{int(time.time())}"
    b.delivery_method = client.enums.BudgetDeliveryMethodEnum.STANDARD
    b.amount_micros   = 3_000 * 1_000_000
    res = svc.mutate_campaign_budgets(customer_id=CUSTOMER_ID, operations=[op])
    return res.results[0].resource_name

def create_campaign(client, budget_rn):
    svc = client.get_service("CampaignService")
    op  = client.get_type("CampaignOperation")
    c   = op.create
    c.name                     = "hanamaru_kango_202504"
    c.status                   = client.enums.CampaignStatusEnum.ENABLED
    c.campaign_budget          = budget_rn
    c.advertising_channel_type = client.enums.AdvertisingChannelTypeEnum.SEARCH
    c.manual_cpc.enhanced_cpc_enabled         = False
    c.network_settings.target_google_search   = True
    c.network_settings.target_search_network  = False
    c.network_settings.target_content_network = False
    c.contains_eu_political_advertising = client.enums.EuPoliticalAdvertisingStatusEnum.DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING
    res = svc.mutate_campaigns(customer_id=CUSTOMER_ID, operations=[op])
    rn  = res.results[0].resource_name
    cid = rn.split("/")[-1]
    return rn, cid

def add_geo_target(client, campaign_rn):
    """日本（location ID: 2392）に絞る"""
    svc = client.get_service("CampaignCriterionService")
    op  = client.get_type("CampaignCriterionOperation")
    cr  = op.create
    cr.campaign = campaign_rn
    cr.location.geo_target_constant = client.get_service(
        "GeoTargetConstantService").geo_target_constant_path("2392")
    svc.mutate_campaign_criteria(customer_id=CUSTOMER_ID, operations=[op])

def add_ad_schedule(client, campaign_rn):
    """平日 9:00-18:00"""
    svc  = client.get_service("CampaignCriterionService")
    ops  = []
    days = [
        client.enums.DayOfWeekEnum.MONDAY,
        client.enums.DayOfWeekEnum.TUESDAY,
        client.enums.DayOfWeekEnum.WEDNESDAY,
        client.enums.DayOfWeekEnum.THURSDAY,
        client.enums.DayOfWeekEnum.FRIDAY,
    ]
    for day in days:
        op = client.get_type("CampaignCriterionOperation")
        cr = op.create
        cr.campaign = campaign_rn
        cr.ad_schedule.day_of_week   = day
        cr.ad_schedule.start_hour    = 9
        cr.ad_schedule.start_minute  = client.enums.MinuteOfHourEnum.ZERO
        cr.ad_schedule.end_hour      = 18
        cr.ad_schedule.end_minute    = client.enums.MinuteOfHourEnum.ZERO
        ops.append(op)
    svc.mutate_campaign_criteria(customer_id=CUSTOMER_ID, operations=ops)

def create_ad_group(client, campaign_rn, name):
    svc = client.get_service("AdGroupService")
    op  = client.get_type("AdGroupOperation")
    ag  = op.create
    ag.name           = name
    ag.campaign       = campaign_rn
    ag.status         = client.enums.AdGroupStatusEnum.ENABLED
    ag.type_          = client.enums.AdGroupTypeEnum.SEARCH_STANDARD
    ag.cpc_bid_micros = 1_000 * 1_000_000  # グループ上限 ¥1,000
    res = svc.mutate_ad_groups(customer_id=CUSTOMER_ID, operations=[op])
    return res.results[0].resource_name

def create_keywords(client, ag_rn, keywords):
    svc = client.get_service("AdGroupCriterionService")
    ops = []
    match_map = {
        "PHRASE": client.enums.KeywordMatchTypeEnum.PHRASE,
        "EXACT":  client.enums.KeywordMatchTypeEnum.EXACT,
    }
    for text, match in keywords:
        op   = client.get_type("AdGroupCriterionOperation")
        cr   = op.create
        cr.ad_group             = ag_rn
        cr.status               = client.enums.AdGroupCriterionStatusEnum.ENABLED
        cr.keyword.text         = text
        cr.keyword.match_type   = match_map[match]
        cr.cpc_bid_micros       = 300 * 1_000_000  # ¥300
        ops.append(op)
    svc.mutate_ad_group_criteria(customer_id=CUSTOMER_ID, operations=ops)
    print(f"  キーワード {len(ops)} 件追加完了")

def create_rsa(client, ag_rn, headlines, descriptions):
    svc = client.get_service("AdGroupAdService")
    op  = client.get_type("AdGroupAdOperation")
    aga = op.create
    aga.ad_group = ag_rn
    aga.status   = client.enums.AdGroupAdStatusEnum.ENABLED

    ad  = aga.ad
    ad.final_urls.append(LP_URL)
    rsa = ad.responsive_search_ad
    rsa.path1 = "訪問看護AI"
    rsa.path2 = "無料トライアル"

    for i, h in enumerate(headlines):
        asset      = client.get_type("AdTextAsset")
        asset.text = h
        if i == 0:
            asset.pinned_field = client.enums.ServedAssetFieldTypeEnum.HEADLINE_1
        rsa.headlines.append(asset)

    for d in descriptions:
        asset      = client.get_type("AdTextAsset")
        asset.text = d
        rsa.descriptions.append(asset)

    svc.mutate_ad_group_ads(customer_id=CUSTOMER_ID, operations=[op])
    print("  広告（RSA）作成完了")

# ============================================================
# メイン
# ============================================================
def main():
    client = GoogleAdsClient.load_from_storage(YAML_PATH)
    print("=== はなまるAI看護版 キャンペーン作成開始 ===")

    print("\n[1] 予算作成...")
    budget_rn = create_budget(client)
    print(f"  完了: {budget_rn}")

    print("\n[2] キャンペーン作成...")
    campaign_rn, campaign_id = create_campaign(client, budget_rn)
    print(f"  完了: ID={campaign_id}")

    print("\n[3] 地域ターゲット（日本）設定...")
    add_geo_target(client, campaign_rn)
    print("  完了")

    print("\n[4] 広告スケジュール（平日9-18時）設定...")
    add_ad_schedule(client, campaign_rn)
    print("  完了")

    print("\n[5] 広告グループA（看護記録効率化）作成...")
    ag_a_rn = create_ad_group(client, campaign_rn, "看護記録効率化")
    create_keywords(client, ag_a_rn, KEYWORDS_A)
    create_rsa(client, ag_a_rn, HEADLINES_A, DESCRIPTIONS_A)

    print("\n[6] 広告グループB（音声・ICT）作成...")
    ag_b_rn = create_ad_group(client, campaign_rn, "音声・ICT")
    create_keywords(client, ag_b_rn, KEYWORDS_B)
    create_rsa(client, ag_b_rn, HEADLINES_B, DESCRIPTIONS_B)

    print("\n=== 作成完了！===")
    print(f"キャンペーンID  : {campaign_id}")
    print(f"LP URL         : {LP_URL}")
    print(f"予算           : ¥3,000/日")
    print(f"入札           : 手動CPC ¥300（上限 ¥1,000）")
    print(f"配信時間       : 平日 9:00-18:00")
    print(f"地域           : 日本")
    print(f"広告グループ   : 2（看護記録効率化 / 音声・ICT）")
    print(f"キーワード計   : {len(KEYWORDS_A) + len(KEYWORDS_B)} 件")

if __name__ == "__main__":
    try:
        main()
    except GoogleAdsException as ex:
        print(f"[NG] API エラー: {ex.error.code().name}")
        for e in ex.failure.errors:
            print(f"  -> {e.message}")
            print(f"     field: {[f.field_name for f in e.location.field_path_elements]}")
    except Exception as ex:
        print(f"[NG] エラー: {ex}")
