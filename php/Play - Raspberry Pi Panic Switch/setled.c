//  Using code from:
//  How to access GPIO registers from C-code on the Raspberry-Pi ,  Dom and Gert

// Access from ARM Running Linux

#define BCM2708_PERI_BASE        0x20000000
#define GPIO_BASE                (BCM2708_PERI_BASE + 0x200000) /* GPIO controller */


#include <stdio.h>
#include <stdbool.h>
#include <string.h>
#include <stdlib.h>
#include <dirent.h>
#include <fcntl.h>
#include <assert.h>
#include <sys/mman.h>
#include <sys/types.h>
#include <sys/stat.h>

#include <unistd.h>

#define PAGE_SIZE (4*1024)
#define BLOCK_SIZE (4*1024)

int  mem_fd;
char *gpio_mem, *gpio_map;
char *spi0_mem, *spi0_map;


// I/O access
volatile unsigned *gpio;


// GPIO setup macros. Always use INP_GPIO(x) before using OUT_GPIO(x) or SET_GPIO_ALT(x,y)
#define INP_GPIO(g) *(gpio+((g)/10)) &= ~(7<<(((g)%10)*3))
#define OUT_GPIO(g) *(gpio+((g)/10)) |=  (1<<(((g)%10)*3))
#define SET_GPIO_ALT(g,a) *(gpio+(((g)/10))) |= (((a)<=3?(a)+4:(a)==4?3:2)<<(((g)%10)*3))

#define GPIO_SET *(gpio+7)  // sets   bits which are 1 ignores bits which are 0
#define GPIO_CLR *(gpio+10) // clears bits which are 1 ignores bits which are 0
#define GPIO_LEV *(gpio+13)  // read back current level on input pin


void setup_io();


char* convertupper( char*t, char *s, int n )
{
  int i=0;
  char c;

  do{
    c = s[i];
    t[i] = toupper(c);
    i++; 
    if(i>n)
    {
     t[i]=0; break;
    }
  }while( c != 0 ); 

  return t;
}




int checkparams(int argc, char **argv, bool *ledon, int *portnum)
{
  bool ok=true;
  char param[8];

  if(argc>1)
  {
    *portnum=1;

    convertupper( param, argv[1], 4 );
    if( strcmp( param,"ON" )==0 )
      *ledon = true;
    else if( strcmp( param,"OFF" )==0 )
      *ledon = false;
    else
      ok=false; 
    if(argc>2)
    {
      *portnum = atoi( argv[2] );
      if(*portnum<0 || *portnum>8)
        ok=false;
      printf("Selected port = %d\n", *portnum); 
    } 
  }
  return ok;
}


int main(int argc, char **argv)
{ 
  bool ledon=false;
  int g=7;
  int portnum;

  if( checkparams(argc,argv,&ledon,&portnum) )
  {
    if(portnum==1)g=7;    //map LED number to GPIO port

    seteuid(0);
  
    setup_io();

    INP_GPIO(g);
    OUT_GPIO(g);

    if(ledon)
    {
      printf("Light is ON\n");
      GPIO_SET = 1<<g;
    }
    else
    {
      printf("Light is OFF\n");
      GPIO_CLR = 1<<g;
    }
    seteuid( getuid() );

    return 0;
  }
 
  return 1;

} // main


//
// Set up a memory regions to access GPIO
//
void setup_io()
{
   /* open /dev/mem */
   if ((mem_fd = open("/dev/mem", O_RDWR|O_SYNC) ) < 0) {
      printf("can't open /dev/mem \n");
      exit (-1);
   }

   /* mmap GPIO */

   // Allocate MAP block
   if ((gpio_mem = malloc(BLOCK_SIZE + (PAGE_SIZE-1))) == NULL) {
      printf("allocation error \n");
      exit (-1);
   }

   // Make sure pointer is on 4K boundary
   if ((unsigned long)gpio_mem % PAGE_SIZE)
     gpio_mem += PAGE_SIZE - ((unsigned long)gpio_mem % PAGE_SIZE);

   // Now map it
   gpio_map = (unsigned char *)mmap(
      (caddr_t)gpio_mem,
      BLOCK_SIZE,
      PROT_READ|PROT_WRITE,
      MAP_SHARED|MAP_FIXED,
      mem_fd,
      GPIO_BASE
   );

   if ((long)gpio_map < 0) {
      printf("mmap error %d\n", (int)gpio_map);
      exit (-1);
   }

   // Always use volatile pointer!
   gpio = (volatile unsigned *)gpio_map;


} // setup_io
