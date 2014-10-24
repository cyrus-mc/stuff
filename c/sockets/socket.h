#ifndef __socket_class
#define __socket_class

#include <stdio.h>
#include <arpa/inet.h>
#include <sys/types.h>
#include <sys/socket.h>
#include "socket_exception.h"

typedef enum {
	UNKNOWN, m_data, f_data
} data_type;

/*
 * Date type definition of packet structure
 */
typedef struct packet_header {
	data_type d_type; // used to specify type of data transfered
	size_t d_size;
} packet; 

class Socket {
	private:
		/* write packet header, return bytes sent */
		inline int write_header(packet *);
		/* read packet header into supplied parameter, return bytes read */
		inline int read_header(packet *);

		/* write data and return # of bytes sent */
		inline int write_data(char *, size_t); 
		inline int write_file(int, size_t);
		inline int read_data(char *, size_t);
		inline int read_file(int, size_t);

	protected:
		int m_sock;
		struct sockaddr_in m_addr;

	public:
		Socket();
		Socket(int);
		~Socket();
		/* member functions common to all sockets */
		void close();

		void write(char *);
		void write(int);
		
		char *read();
		void read(int);
};
#endif
