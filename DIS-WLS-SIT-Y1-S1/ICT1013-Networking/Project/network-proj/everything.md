Access Switch 1 

en
conf t
hostname S1
vlan 10 
name Meeting_RMs
vlan 20
name CS
vlan 40 
name Networking
vlan 60
name HR
spanning-tree mode rapid-pvst
spanning-tree vlan 20,40,60 priority 61440
interface g1/0/17
switchport mode access
swithcport access vlan 10
spanning-tree portfast
spanning-tree bpduguard enable
interface g1/0/13
switchport mode access
switchport access vlan 40
spanning-tree portfast
spanning-tree bpduguard enable
interface g1/0/14
switchport mode access
switchport access vlan 60
spanning-tree portfast
spanning-tree bpduguard enable
interface range g1/0/1-12
switchport mode access
switchport access vlan 20
spanning-tree portfast
spanning-tree bpduguard enable
int range g1/0/15, g1/0/20
channel-group 1 mode desirable
no shut
int po1 
switchport mode trunk
switchport trunk allowed vlan 20,40,60
spanning-tree link-type point-to-point
int range g1/0/16, g1/0/21
channel-group 2 mode desirable
no shut
int po2 
switchport mode trunk
switchport trunk allowed vlan 20,40,60
spanning-tree link-type point-to-point



Access Switch 2

en
conf t
hostname S2
vlan 30
name Marketing
vlan 40
name Networking
vlan 60
name HR
spanning-tree mode rapid-pvst
spanning-tree vlan 30,40,60 priority 61440
interface g1/0/22
switchport mode access
switchport access vlan 40
spanning-tree portfast
spanning-tree bpduguard enable
interface g1/0/21
switchport mode access
switchport access vlan 60
spanning-tree portfast
spanning-tree bpduguard enable
interface range g1/0/1-10
switchport mode access
switchport access vlan 30
spanning-tree portfast
spanning-tree bpduguard enable
int range g1/0/11, g1/0/16
channel-group 1 mode desirable
no shut
int po1 
switchport mode trunk
switchport trunk allowed vlan 30,40,60
spanning-tree link-type point-to-point
int range g1/0/12, g1/0/17
channel-group 2 mode desirable
no shut
int po2 
switchport mode trunk
switchport trunk allowed vlan 30,40,60
spanning-tree link-type point-to-point



Access Switch 3

en
conf t
hostname S3
vlan 40
name Networking
vlan 50
name Solutions
vlan 60
name HR
vlan 80
name Servers
spanning-tree mode rapid-pvst
spanning-tree vlan 40,50,60,80 priority 61440
interface g1/0/4
switchport mode access
switchport access vlan 60
spanning-tree portfast
spanning-tree bpduguard enable
interface range g1/0/5-7
switchport mode access
switchport access vlan 40
spanning-tree portfast
spanning-tree bpduguard enable
interface range g1/0/8-10, g1/0/13-14
switchport mode access
switchport access vlan 50
spanning-tree portfast
spanning-tree bpduguard enable
interface range g1/0/1-3
switchport mode access
switchport access vlan 80
spanning-tree portfast
spanning-tree bpduguard enable
int range g1/0/11, g1/0/16
channel-group 1 mode desirable
no shut
int po1 
switchport mode trunk
switchport trunk allowed vlan 40,50,60,80
spanning-tree link-type point-to-point
int range g1/0/12, g1/0/17
channel-group 2 mode desirable
no shut
int po2 
switchport mode trunk
switchport trunk allowed vlan 40,50,60,80
spanning-tree link-type point-to-point



Access Switch 4 
wheres my vlan 10

en
conf t
hostname S4
vlan 10 
name Meeting_RMs
vlan 40
name Networking
vlan 60
name HR
vlan 70
name Managers
spanning-tree mode rapid-pvst
spanning-tree vlan 40,60,70 priority 61440
interface g1/0/17
switchport mode access
spanning-tree portfast
spanning-tree bpduguard enable
swithcport access vlan 10
interface g1/0/3
switchport mode access
switchport access vlan 40
spanning-tree portfast
spanning-tree bpduguard enable
interface range g1/0/8-9
switchport mode access
switchport access vlan 60
spanning-tree portfast
spanning-tree bpduguard enable
interface range g1/0/4-7
switchport mode access
switchport access vlan 70
spanning-tree portfast
spanning-tree bpduguard enable
int range g1/0/1, g1/0/13
channel-group 1 mode desirable
no shut
int po1 
switchport mode trunk
switchport trunk allowed vlan 40,60,70
spanning-tree link-type point-to-point
int range g1/0/2, g1/0/14
channel-group 2 mode desirable
no shut
int po2 
switchport mode trunk
switchport trunk allowed vlan 40,60,70
spanning-tree link-type point-to-point


CS1

en
conf t
hostname C1
vlan 10
name Meeting_RMs
vlan 20
name CS
vlan 30
name Marketing
vlan 40
name Networking
vlan 50
name Solutions
vlan 60
name HR
vlan 70
name Managers
vlan 80
name Servers
vlan 99
name Switch_MGM
spanning-tree mode rapid-pvst
spanning-tree vlan 1,10,20,30,40,50,60,70,80,99 root primary
int range g1/0/6, g1/0/11
channel-group 1 mode auto
spanning-tree guard root
int po1
switchport trunk allowed vlan 20,40,60
spanning-tree link-type point-to-point
no shut
int range g1/0/5, g1/0/10
channel-group 2 mode auto
spanning-tree guard root
int po2
switchport trunk allowed vlan 30,40,60
spanning-tree link-type point-to-point
no shut
int range g1/0/3, g1/0/9
channel-group 3 mode auto
spanning-tree guard root
int po3
switchport trunk allowed vlan 40,50,60,80
spanning-tree link-type point-to-point
no shut
int range g1/0/4, g1/0/8
channel-group 4 mode auto
spanning-tree guard root
int po4
switchport trunk allowed vlan 40,60,70
spanning-tree link-type point-to-point
spanning-tree guard root
no shut
int range g1/0/7, g1/0/12
no switchport
channel-group 5 mode on
int po5
switchport trunk allowed vlan 99
ip add 172.16.1.180 255.255.255.252

ip routing

interface Vlan10
ip address 172.16.1.2 255.255.255.192
standby 10 ip 172.16.1.1
standby 10 priority 110
standby 10 preempt

interface Vlan20
ip address 172.16.1.66 255.255.255.224
standby 20 ip 172.16.1.65
standby 20 priority 110
standby 20 preempt

interface Vlan30
ip address 172.16.1.98 255.255.255.240
standby 30 ip 172.16.1.97
standby 30 priority 110
standby 30 preempt

interface Vlan40
ip address 172.16.1.114 255.255.255.240
standby 40 ip 172.16.1.113
standby 40 priority 110
standby 40 preempt

interface Vlan50
ip address 172.16.1.130 255.255.255.240
standby 50 ip 172.16.1.129
standby 50 priority 110
standby 50 preempt

interface Vlan60
ip address 172.16.1.146 255.255.255.240
standby 60 ip 172.16.1.145
standby 60 priority 110
standby 60 preempt

interface Vlan70
ip address 172.16.1.162 255.255.255.240
standby 70 ip 172.16.1.161
standby 70 priority 110
standby 70 preempt

interface Vlan80
ip address 172.16.1.194 255.255.255.248
standby 80 ip 172.16.1.193
standby 80 priority 110
standby 80 preempt

interface Vlan99
ip address 172.16.1.178 255.255.255.240
standby 99 ip 172.16.1.177
standby 99 priority 110
standby 99 preempt

interface g1/0/1
no switchport
ip add 172.16.1.201 255.255.255.252
interface g1/0/2
no switchport 
ip add 172.16.1.205 255.255.255.252
ip routing
ip route 0.0.0.0 0.0.0.0 gigabitEthernet 1/0/2 172.16.1.206
ip route 0.0.0.0 0.0.0.0 gigabitEthernet 1/0/1 172.16.1.202 10












CS2

en
conf t
hostname C2
vlan 10
name Meeting_RMs
vlan 20
name CS
vlan 30
name Marketing
vlan 40
name Networking
vlan 50
name Solutions
vlan 60
name HR
vlan 70
name Managers
vlan 80
name Servers
vlan 99
name Switch_MGM
spanning-tree mode rapid-pvst
spanning-tree vlan 1,10,20,30,40,50,60,70,80,99 root secondary
int range g1/0/5, g1/0/10
channel-group 1 mode auto
int po1
switchport trunk allowed vlan 20,40,60
spanning-tree link-type point-to-point
no shut
int range g1/0/4, g1/0/11
channel-group 2 mode auto
int po2
switchport trunk allowed vlan 30,40,60
spanning-tree link-type point-to-point
no shut
int range g1/0/6, g1/0/12
channel-group 3 mode auto
int po3
switchport trunk allowed vlan 40,50,60,80
spanning-tree link-type point-to-point
no shut
int range g1/0/3, g1/0/9
channel-group 4 mode auto
int po4
switchport trunk allowed vlan 40,60,70
spanning-tree link-type point-to-point
no shut
int range g1/0/7, g1/0/8
no switchport
channel-group 5 mode on 
int po5
ip add 172.16.1.181 255.255.255.252

interface Vlan10
 ip address 172.16.1.3 255.255.255.192
 standby 10 ip 172.16.1.1
 standby 10 priority 90
 standby 10 preempt

interface Vlan20
 ip address 172.16.1.67 255.255.255.224
 standby 20 ip 172.16.1.65
 standby 20 priority 90
 standby 20 preempt

interface Vlan30
 ip address 172.16.1.99 255.255.255.240
 standby 30 ip 172.16.1.97
 standby 30 priority 90
 standby 30 preempt

interface Vlan40
 ip address 172.16.1.115 255.255.255.240
 standby 40 ip 172.16.1.113
 standby 40 priority 90
 standby 40 preempt

interface Vlan50
 ip address 172.16.1.131 255.255.255.240
 standby 50 ip 172.16.1.129
 standby 50 priority 90
 standby 50 preempt

interface Vlan60
 ip address 172.16.1.147 255.255.255.240
 standby 60 ip 172.16.1.145
 standby 60 priority 90
 standby 60 preempt

interface Vlan70
 ip address 172.16.1.163 255.255.255.240
 standby 70 ip 172.16.1.161
 standby 70 priority 90
 standby 70 preempt

interface Vlan80
 ip address 172.16.1.195 255.255.255.248
 standby 80 ip 172.16.1.193
 standby 80 priority 90
 standby 80 preempt

interface Vlan99
 ip address 172.16.1.179 255.255.255.240
 standby 99 ip 172.16.1.177
 standby 99 priority 90
 standby 99 preempt

interface g1/0/1
no switchport
no shutdown
ip add 172.16.1.209 255.255.255.252
interface g1/0/2
no switchport 
no shutdown
ip add 172.16.1.213 255.255.255.252
ip routing
ip route 0.0.0.0 0.0.0.0 gigabitEthernet 1/0/2 172.16.1.214
ip route 0.0.0.0 0.0.0.0 gigabitEthernet 1/0/1 172.16.1.210 10

#c1 g1/0/1 172.16.1.205/30 r1 g0/0/1 172.16.1.206/30
#c1 g1/0/2 172.16.1.209/30 r2 g0/0/1 172.16.1.210/30
#c2 g1/0/1 172.16.1.213/30 r2 g0/0/0 172.16.1.214/30
#c2 g1/0/2 172.16.1.217/30 r1 g0/0/0 172.16.1.218/30


#c1 g1/0/1 172.16.1.201/30 r1 g0/0/1 172.16.1.202/30
#c1 g1/0/2 172.16.1.205/30 r2 g0/0/1 172.16.1.206/30
#c2 g1/0/1 172.16.1.209/30 r2 g0/0/0 172.16.1.210/30
#c2 g1/0/2 172.16.1.213/30 r1 g0/0/0 172.16.1.214/30


r1
en
conf t
hostname r1
interface g0/0/0
no shutdown
ip add 172.16.1.214 255.255.255.252
interface g0/0/1
no shutdown
ip add 172.16.1.202 255.255.255.252
ip routing
ip route 172.16.1.0 255.255.255.0 gigabitEthernet 0/0/1 172.16.1.201
ip route 172.16.1.0 255.255.255.0 gigabitEthernet 0/0/0 172.16.1.213 10

<!-- ip nat inside source static tcp 172.16.1.196 80 230.149.210.2 80
ip nat inside source static tcp 172.16.1.196 443 230.149.210.2 443

ip route 0.0.0.0 0.0.0.0 230.149.210.1 -->

r2
en
conf t
hostname r2
interface g0/0/0
no shutdown
ip add 172.16.1.210 255.255.255.252
interface g0/0/1
no shutdown
ip add 172.16.1.206 255.255.255.252
ip routing
ip route 172.16.1.0 255.255.255.0 gigabitEthernet 0/0/1 172.16.1.205
ip route 172.16.1.0 255.255.255.0 gigabitEthernet 0/0/0 172.16.1.209 10


11 - 4(11) - 6 = f | 4f = last octet of assigned address block 172.17.9.XX
router to isp = first usable
230.149.210 + [8f // 256].296%256 = public IP for ISP1 

address block 172.17.10.XX
129.126.142 + [8f // 256].296%256 = public IP for ISP2 

--
Assuming We use Rack 1A, R1(edge router) uses int(ID007), R2(edge router) uses int(ID009)

R1
Interface Port: ID007
PTP IP Block: 172.17.9.4/30
R1 Int(G0/1/1): 172.17.9.5/30
Public IP block: 203.149.210.8/29

R2
Interface Port: ID009
PTP IP Block: 172.17.10.4/30
R2 INT(G0/1/1): 172.17.10.5/30
Public IP block: 129.126.142.8/29

# NAT Stuff for R1
en
conf t
int g0/1/1
ip address 203.149.210.9 255.255.255.248
ip address 172.17.9.5 255.255.255.252 secondary
ip nat outside 
no shutdown
exit
int range g0/0/0 - 1
ip nat inside
no shutdown
exit
# Static NAT for Webserver only
ip nat inside source static 172.16.1.196 203.149.210.10
# PAT for whole Network
access-list 1 permit 172.16.1.0 0.0.0.255
ip nat inside source list 1 interface g0/1/1 overload
ip route 0.0.0.0 0.0.0.0 172.17.9.6




# NAT Stuff for R2
en 
conf t
int g0/1/1
ip address 129.126.142.9 255.255.255.248
ip address 172.17.10.5 255.255.255.252 secondary
ip nat outside
no shutdown
int range g0/0/0 - 1
ip nat inside
no shutdown
exit
# Static NAT for Webserver
ip nat inside source static 172.16.1.196 129.126.142.10
# PAT for whole Network
access-list 1 permit 172.16.1.0 0.0.0.255
ip nat inside source list 1 interface g0/1/1 overload
ip route 0.0.0.0 172.17.10.6
